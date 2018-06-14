<?php
/**
 * ApontamentoReport Report
 * @author  <your name here>
 */
class ApontamentoReport extends TPage
{
    protected $form; // form
    protected $notebook;
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Apontamento_report');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Apontamento Report');
        


        // create the form fields
        $usuario_login = new TEntry('usuario_login');
        $ordem_nr = new TEntry('ordem_nr');
        $quantidade = new TEntry('quantidade');
        $perda_descricao = new TEntry('perda_descricao');
        $qtde_perda = new TEntry('qtde_perda');
        $dataAp = new TEntry('dataAp');
        $output_type = new TRadioGroup('output_type');


        // add the fields
        $this->form->addQuickField('Usuario', $usuario_login,  100 );
        $this->form->addQuickField('Ordem', $ordem_nr,  50 );
        $this->form->addQuickField('Qtde', $quantidade,  80 );
        $this->form->addQuickField('Perda', $perda_descricao,  100 );
        $this->form->addQuickField('Qtde Perda', $qtde_perda,  50 );
        $this->form->addQuickField('Data', $dataAp,  150 );
        $this->form->addQuickField('Output', $output_type,  100 , new TRequiredValidator);



        
        $output_type->addItems(array('html'=>'HTML', 'pdf'=>'PDF', 'rtf'=>'RTF'));;
        $output_type->setValue('pdf');
        $output_type->setLayout('horizontal');
        
        // add the action button
        $this->form->addQuickAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
    /**
     * Generate the report
     */
    function onGenerate()
    {
        try
        {
            // open a transaction with database 'gestor'
            TTransaction::open('gestor');
            
            // get the form data into an active record
            $formdata = $this->form->getData();
            
        $criteria1 = new TCriteria;
        //$criteria1->setProperty('limit',5);
        $criteria1->add( new TFilter('(SELECT SUM(quantidade) as totalOS, numero from gestor_ordemgroup by numero)'));
        
        parent::setCriteria($criteria1); // define a standard filter
            
            
            $repository = new TRepository('Apontamento');
            $criteria   = new TCriteria;
            
            if ($formdata->usuario_login)
            {
                $criteria->add(new TFilter('(select login from system_user where id = system_user_id)', 'like', "%{$formdata->usuario_login}%"));
            }
            if ($formdata->ordem_nr)
            {
                $criteria->add(new TFilter('(select numero from gestor_ordem where id = ordem_id)', 'like', "%{$formdata->ordem_nr}%"));
            }
            if ($formdata->quantidade)
            {
                $criteria->add(new TFilter('quantidade', 'like', "%{$formdata->quantidade}%"));
            }
            if ($formdata->perda_descricao)
            {
                $criteria->add(new TFilter('(select descricao from gestor_perda where id = perda_id)', 'like', "%{$formdata->perda_descricao}%"));
            }
            if ($formdata->qtde_perda)
            {
                $criteria->add(new TFilter('qtde_perda', 'like', "%{$formdata->qtde_perda}%"));
            }
            if ($formdata->dataAp)
            {
                $criteria->add(new TFilter('dataAp', 'like', "%{$formdata->dataAp}%"));
            }

           
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $widths = array(50,50,50,50,50,100);
                
                switch ($format)
                {
                    case 'html':
                        $tr = new TTableWriterHTML($widths);
                        break;
                    case 'pdf':
                        $tr = new TTableWriterPDF($widths);
                        break;
                    case 'rtf':
                        if (!class_exists('PHPRtfLite_Autoloader'))
                        {
                            PHPRtfLite::registerAutoloader();
                        }
                        $tr = new TTableWriterRTF($widths);
                        break;
                }
                
                // create the document styles
                $tr->addStyle('title', 'Arial', '10', 'B',   '#ffffff', '#A3A3A3');
                $tr->addStyle('datap', 'Arial', '10', '',    '#000000', '#EEEEEE');
                $tr->addStyle('datai', 'Arial', '10', '',    '#000000', '#ffffff');
                $tr->addStyle('header', 'Arial', '16', '',   '#ffffff', '#6B6B6B');
                $tr->addStyle('footer', 'Times', '10', 'I',  '#000000', '#A3A3A3');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Apontamento', 'center', 'header', 6);
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Usuário', 'right', 'title');
                $tr->addCell('Ordem', 'right', 'title');
                $tr->addCell('Qtde', 'right', 'title');
                $tr->addCell('Perda', 'right', 'title');
                $tr->addCell('Qtde Perda', 'right', 'title');
                $tr->addCell('Data', 'left', 'title');

                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->system_user_id, 'right', $style);
                    $tr->addCell($object->ordem_id, 'right', $style);
                    $tr->addCell($object->quantidade, 'right', $style);
                    $tr->addCell($object->perda_id, 'right', $style);
                    $tr->addCell($object->qtde_perda, 'right', $style);
                    $tr->addCell($object->dataAp, 'left', $style);

                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 6);
                // stores the file
                if (!file_exists("app/output/Apontamento.{$format}") OR is_writable("app/output/Apontamento.{$format}"))
                {
                    $tr->save("app/output/Apontamento.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Apontamento.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Apontamento.{$format}");
                
                // shows the success message
                new TMessage('info', 'Report generated. Please, enable popups.');
            }
            else
            {
                new TMessage('error', 'No records found');
            }
    
            // fill the form with the active record data
            $this->form->setData($formdata);
            
            // close the transaction
            TTransaction::close();
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            
            // undo all pending operations
            TTransaction::rollback();
        }
    }
}
