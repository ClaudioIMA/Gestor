<?php
/**
 * DiarioForm Registration
 * @author  <your name here>
 */
class DiarioForm extends TPage
{
    protected $form; // form
    
    use Adianti\Base\AdiantiStandardFormTrait; // Standard form methods
    
    /**
     * Class constructor
     * Creates the page and the registration form
     */
    function __construct()
    {
        parent::__construct();
        
        
        $this->setDatabase('gestor');              // defines the database
        $this->setActiveRecord('Diario');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Diario');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Diario');

        // create the form fields
        $id = new TEntry('id');
        $parada_id = new TDBCombo('parada_id','gestor','Parada','id','descricao');
        $system_user_id = new TDBCombo('system_user_id','gestor','SystemUser','id','login');
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $data_inicio = new THidden('data_inicio');
        $data_fim = new THidden('data_fim');
        
        if (empty($_COOKIE['equipAtual']))
        {
            AdiantiCoreApplication::loadPage('EquipamentoSelect');
            
        }
        else 
        {
            $maquina = $_COOKIE['equipAtual'];
            $eqpto_id->setValue($maquina);
            $eqpto_id->setEditable(false);  
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
        $this->form->addQuickField('Parada', $parada_id,  200 );
        $this->form->addQuickField('Usuario', $system_user_id,  100 );
        $this->form->addQuickField('Eqpto', $eqpto_id,  100 );
        $this->form->addQuickField('Data Inicio', $data_inicio,  100 );
        $this->form->addQuickField('Data Fim', $data_fim,  100 );
        $data_inicio->setValue(date('Y-m-d H:i:s'));
        $id->setSize(100,30);
        $system_user_id->setSize(200,30);
        $parada_id->setSize(200,30);
        $eqpto_id->setSize(200,30);
        
        
        if (!empty($id))
        {
            $id->setEditable(FALSE);
        }
        
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        //$this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        $this->form->addQuickAction(_t('Resume'),  new TAction(array($this, 'onLibera')),'fa:floopy-o');
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    
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
            
            $object = new Diario;  // create an empty object
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
            $this->form->setData( $this->form->clear(True)); // Clear form data
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
    public function onLibera( $param )
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
            
            $diario = new Diario;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $diario->fromArray( (array) $data); // load the object with data
            $diario->data_fim = date('Y-m-d H:i:s');
            
                //Se for edição salva e retorna, senao retorna sem salvar
                if ($data->id){
            
                $diario->store(); // save the object
            
                 // get the generated id
                $data->id = $diario->id;
                TTransaction::close(); // close the transaction
                AdiantiCoreApplication::loadPage('ApontamentoForm');
                }else{
                TTransaction::close(); // close the transaction
                AdiantiCoreApplication::loadPage('ApontamentoForm');
                }
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            $this->form->setData( $this->form->clear(True)); // Clear form data
            TTransaction::rollback(); // undo all pending operations
        }
    }

}
    