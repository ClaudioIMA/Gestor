<?php
/**
 * ApontamentoForm Form
 * @author  <your name here>
 */
class ApontamentoTablet extends TPage
{
    protected $form; // form
    
    /**
     * Form constructor
     * @param $param Request
     */
    public function __construct( $param )
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_Apontamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Apontamento');
        


        // create the form fields
        $id = new THidden('id');
        $system_user_id = new TEntry('system_user_id');
        $ordem_id = new TCombo('ordem_id');
        $quantidade = new TEntry('quantidade');
        $perda_id = new TEntry('perda_id');
        $qtde_perda = new TEntry('qtde_perda');
        $dataAp = new THidden('dataAp');

        try
                {
                TTransaction::open('gestor');
                $osproduzindo = array();
                $criterio = new TCriteria;
                $criterio->setProperty('limit',5);
                $criterio->add( new TFilter('status', 'like','produzindo'));
                $repo = new TRepository('Ordem');
                $objetos = $repo->load($criterio);
                
                    foreach ($objetos as $objeto)
                    {  
                     $osproduzindo[$objeto->id]= $objeto->numero;
                    }
                    $ordem_id->addItems($osproduzindo);
                    TTransaction::close();
                }
        catch (Exception $e)
                {
                TMessage('Erro', $e->getMessage());
                }
                
                try
                {
                TTransaction::open('gestor');
                $criterio = new TCriteria;
                $criterio->setProperty('limit',1);
                $criterio->add( new TFilter('login', '=',TSession::getValue('login')));
                $repo = new TRepository('SystemUser');
                $objetos = $repo->load($criterio);
                
                foreach ($objetos as $objeto)
                {   
                 $system_user_id->setValue($objeto->login);
                 $system_user_id->setEditable(false);
                 if (TSession::getValue('login') == 'admin')
                {
                  $system_user_id->setEditable(true);
                }
                 
                }
                TTransaction::close();
                
                }
        catch (Exception $e)
                {
                TMessage('Erro', $e->getMessage());
                }
        
        // add the fields
        $this->form->addQuickField('Id', $id,  100 );
        $this->form->addQuickField('Usuario', $system_user_id,  100 );
        $this->form->addQuickField('Ordem', $ordem_id,  100 );
        $this->form->addQuickField('Quantidade', $quantidade,  100 );
        $this->form->addQuickField('Cod. Perda', $perda_id,  100 );
        $this->form->addQuickField('Qtde Perda', $qtde_perda,  100 );
        $this->form->addQuickField('Dataap', $dataAp,  200 );
        $dataAp->setValue(date('Y-m-d H:i'));



        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onClear')), 'bs:plus-sign green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }

    /**
     * Save form data
     * @param $param Request
     */
    public function onSave( $param )
    {
        try
        {
            TTransaction::open('gestor'); // open a transaction
            
            /**
            // Enable Debug logger for SQL operations inside the transaction
            TTransaction::setLogger(new TLoggerSTD); // standard output
            TTransaction::setLogger(new TLoggerTXT('log.txt')); // file
            **/
            
            $this->form->validate(); // validate form data
            
            $object = new Apontamento;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            $object->store(); // save the object
            
            // get the generated id
            $data->id = $object->id;
            
            $this->form->setData($data); // fill form data
            TTransaction::close(); // close the transaction
            
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->getData() ); // keep form data
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Clear form data
     * @param $param Request
     */
    public function onClear( $param )
    {
        $this->form->clear(TRUE);
    }
    
    /**
     * Load object to form data
     * @param $param Request
     */
    public function onEdit( $param )
    {
        try
        {
            if (isset($param['key']))
            {
                $key = $param['key'];  // get the parameter $key
                TTransaction::open('gestor'); // open a transaction
                $object = new Apontamento($key); // instantiates the Active Record
                $this->form->setData($object); // fill the form
                TTransaction::close(); // close the transaction
            }
            else
            {
                $this->form->clear(TRUE);
            }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
}
