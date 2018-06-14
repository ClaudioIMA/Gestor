<?php
/**
 * OrdemReport Report
 * @author  <your name here>
 */
class OrdemReport extends TPage
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
        $this->form = new TQuickForm('form_Ordem_report');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Ordem Report');
        


        // create the form fields
        $numero = new TEntry('numero');
        $produto_id = new TDBCombo('produto_id','gestor','Produto','id','codigo');
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');
        $status = new TEntry('status');
        $output_type = new TRadioGroup('output_type');


        // add the fields
        $this->form->addQuickField('Ordem', $numero,  50 );
        $this->form->addQuickField('Produto', $produto_id,  150 );
        $this->form->addQuickField('Data Inicio', $data_inicio,  100 );
        $this->form->addQuickField('Data Fim', $data_fim,  100 );
        $this->form->addQuickField('Status', $status,  100 );
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
            
            $repository = new TRepository('Ordem');
            $criteria   = new TCriteria;
            
            if ($formdata->numero)
            {
                $criteria->add(new TFilter('numero', 'like', "%{$formdata->numero}%"));
            }
            if ($formdata->produto_id)
            {
                $criteria->add(new TFilter('produto_id', 'like', "%{$formdata->produto_id}%"));
            }
            if ($formdata->data_inicio)
            {
                $criteria->add(new TFilter('data_inicio', 'BETWEEN', "{$formdata->data_inicio}","{$formdata->data_fim}"));
            }
            //if ($formdata->data_fim)
            //{
            //    $criteria->add(new TFilter('data_fim', '', "%{$formdata->data_fim}%"));
            //}
            if ($formdata->status)
            {
                $criteria->add(new TFilter('status', 'like', "%{$formdata->status}%"));
            }
           
            $objects = $repository->load($criteria, FALSE);
            $format  = $formdata->output_type;
            
            if ($objects)
            {
                $widths = array(60,95,60,60,60,95,70);
                
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
                $tr->addStyle('vazio',  'Arial', '8' , '',   '#ffffff', '#ffffff');
                
                // add a header row
                $tr->addRow();
                $tr->addCell('Relatório de Ordens - RRID', 'center', 'header', 7);
                
                $tr->addRow();
                $tr->addCell('','center','vazio',7);
                
                // add titles row
                $tr->addRow();
                $tr->addCell('Ordem', 'right', 'title');
                $tr->addCell('Produto', 'right', 'title');
                $tr->addCell('Previsto', 'right', 'title');
                $tr->addCell('Realizado', 'right', 'title');
                $tr->addCell('Perda %', 'right', 'title');
                $tr->addCell('Data Inicio', 'center', 'title');
                $tr->addCell('Status', 'right', 'title');

                
                // controls the background filling
                $colour= FALSE;
                
                // data rows
                foreach ($objects as $object)
                {
                    $total = 0;
                    $perda = 0;
                    $percentual = 0;
                    $criterio = new TCriteria;
                    $criterio->add(new TFilter('ordem_id', '=', $object->id));
                    $repositorio = new TRepository('Apontamento'); 
                    $objetos1 = $repositorio->load($criterio); 
                    foreach ($objetos1 as $objeto1)
                    {
                       $total+= $objeto1->quantidade;
                       $perda+= $objeto1->qtde_perda;
                       $percentual = ($perda/$total)*100;
                    } 
                    
                    //$key = $object->produto_id;
                    $objetoprod = new Produto($object->produto_id);
                    $style = $colour ? 'datap' : 'datai';
                    $tr->addRow();
                    $tr->addCell($object->numero, 'right', $style);
                    $tr->addCell($objetoprod->codigo, 'right', $style);
                    $tr->addCell(number_format($object->qtde, 0, ',', '.'), 'right', $style);
                    $tr->addCell(number_format($total,0, ',', '.'),'right', $style);
                    $tr->addCell(number_format($percentual,2, ',', '.'),'right', $style);
                    $tr->addCell(substr($object->data_inicio,0,10), 'center', $style);
                    $tr->addCell($object->status, 'right', $style);
                    
                    $colour = !$colour;
                }
                
                // footer row
                $tr->addRow();
                $tr->addCell(date('Y-m-d h:i:s'), 'center', 'footer', 7);
                // stores the file
                if (!file_exists("app/output/Ordem.{$format}") OR is_writable("app/output/Ordem.{$format}"))
                {
                    $tr->save("app/output/Ordem.{$format}");
                }
                else
                {
                    throw new Exception(_t('Permission denied') . ': ' . "app/output/Ordem.{$format}");
                }
                
                // open the report file
                parent::openFile("app/output/Ordem.{$format}");
                
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
