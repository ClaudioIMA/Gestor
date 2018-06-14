<?php
/**
 * AgendaForm Form
 * @author  <your name here>
 */
class AgendaForm extends TPage
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
        $this->form = new TQuickForm('form_Agenda');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Agenda');
        


        // create the form fields
        $id = new THidden('id');
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $ordem_id = new TDBCombo('ordem_id','gestor','Ordem','id','numero');
        $system_user_id = new TDBCombo('system_user_id','gestor','SystemUser','id','login'); 
        $data_inicio = new TDateTime('data_inicio');
        $data_previsao = new TLabel('data_previsao');

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
                 //print $objeto->login . ' usuario'; 
                 $system_user_id->setValue($objeto->id);
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
        $this->form->addQuickField('Eqpto Id', $eqpto_id,  100 , new TRequiredValidator);
        $this->form->addQuickField('Ordem Id', $ordem_id,  100 , new TRequiredValidator);
        $this->form->addQuickField('System User Id', $system_user_id,  100 , new TRequiredValidator);
        $this->form->addQuickField('Data Inicio', $data_inicio,  200 , new TRequiredValidator);
        $this->form->addQuickField('Data Previsao', $data_previsao,  200 );
        $system_user_id->setSize(200,50);
    


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
            
            $object = new Agenda;  // create an empty object
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
                $object = new Agenda($key); // instantiates the Active Record
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
