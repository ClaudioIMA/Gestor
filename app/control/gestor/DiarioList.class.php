<?php
/**
 * DiarioList Listing
 * @author  <your name here>
 */
class DiarioList extends TStandardList
{
    protected $form;     // registration form
    protected $datagrid; // listing
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;
    
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        parent::setDatabase('gestor');            // defines the database
        parent::setActiveRecord('Diario');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        
         if (empty($_COOKIE['equipAtual']))
        {
            AdiantiCoreApplication::loadPage('EquipamentoSelect');
            
        }
        else 
        {
            $maquina = $_COOKIE['equipAtual'];
         
        }
        
        
        $criteria = new TCriteria;
        $criteria->setProperty('limit',5);
        $criteria->add( new TFilter('data_fim', 'IS',NULL));
        $criteria->add( new TFilter('eqpto_id', '=',$maquina));
        
        parent::setCriteria($criteria); // define a standard filter

        parent::addFilterField('id', 'like', 'id'); // filterField, operator, formField
        parent::addFilterField('parada_id', 'like', 'parada_id'); // filterField, operator, formField
        parent::addFilterField('(select login from system_user where id = system_user_id)', 'like', 'system_user_id');
        parent::addFilterField('eqpto_id', '=', 'eqpto_id'); // filterField, operator, formField
        parent::addFilterField('data_inicio', 'like', 'data_inicio'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Diario');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Diario');
        

        // create the form fields
        $id = new TEntry('id');
        $parada_id = new TEntry('parada_id');
        $system_user_id = new TEntry('system_user_id');
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $data_inicio = new TEntry('data_inicio');
        

        // add the fields
        $this->form->addQuickField('Id', $id,  200 );
        $this->form->addQuickField('Parada', $parada_id,  200 );
        $this->form->addQuickField('Usuario', $system_user_id,  200 );
        $this->form->addQuickField('Equipamento', $eqpto_id,  200 );
        $this->form->addQuickField('Data Inicio', $data_inicio,  200 );
        
        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Diario_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        //$this->form->addQuickAction(_t('New'),  new TAction(array('DiarioForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'center');
        $column_parada_id = new TDataGridColumn('Parada->descricao', 'Motivo', 'left');
        $column_system_user_id = new TDataGridColumn('System_User->login', 'Usuario', 'right');
        $column_eqpto_id = new TDataGridColumn('Equipamento->name', 'Maquina', 'right');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data Inicio', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_parada_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_eqpto_id);
        $this->datagrid->addColumn($column_data_inicio);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('DiarioForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        //$action_del = new TDataGridAction(array($this, 'onDelete'));
        //$action_del->setUseButton(TRUE);
        //$action_del->setButtonClass('btn btn-default');
        //$action_del->setLabel(_t('Delete'));
        //$action_del->setImage('fa:trash-o red fa-lg');
        //$action_del->setField('id');
        //$this->datagrid->addAction($action_del);
        
        // create the datagrid model
        $this->datagrid->createModel();
        
        // create the page navigation
        $this->pageNavigation = new TPageNavigation;
        $this->pageNavigation->setAction(new TAction(array($this, 'onReload')));
        $this->pageNavigation->setWidth($this->datagrid->getWidth());
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        $container->add($this->datagrid);
        $container->add($this->pageNavigation);
        
        parent::add($container);
    }
    

}
