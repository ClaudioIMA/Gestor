<?php
/**
 * ApontamentoList Listing
 * @author  <your name here>
 */
class ApontamentoList extends TStandardList
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
        parent::setActiveRecord('Apontamento');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('(select descricao from gestor_perda where id = perda_id)', 'like', 'perda_descricao'); 
        parent::addFilterField('(select login from system_user where id = system_user_id)', 'like', 'user_login'); // filterField, operator, formField
        parent::addFilterField('(select numero from gestor_ordem where id = ordem_id)', 'like', 'ordem_numero'); // filterField, operator, formField
        parent::addFilterField('eqpto_id', '=', 'eqpto_id'); // filterField, operator, formField
        
        parent::addFilterField('quantidade', 'like', 'quantidade'); // filterField, operator, formField
        parent::addFilterField('dataAp', 'like', 'dataAp'); // filterField, operator, formField
        // creates the form
        $this->form = new TQuickForm('form_search_Apontamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Apontamento');
        

        // create the form fields
        $user_login = new TEntry('user_login');
        $ordem_numero = new TEntry('ordem_numero');
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $quantidade = new TEntry('quantidade');
        $perda_descricao = new TEntry('perda_descricao');
        $perda_id = new TEntry('perda_id');
        $dataAp = new TEntry('dataAp');


        // add the fields
        $this->form->addQuickField('Usuario', $user_login,  100 );
        $this->form->addQuickField('Ordem', $ordem_numero,  100 );
        $this->form->addQuickField('Equipamento',$eqpto_id,100);
        $this->form->addQuickField('Perda', $perda_descricao,  200 );
        $this->form->addQuickField('Data', $dataAp,  100 );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Apontamento_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ApontamentoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->login', 'Usuario', 'right');
        $column_ordem_id = new TDataGridColumn('Ordem->numero', 'Ordem', 'right');
        $column_equipamento_id = new TDataGridColumn('Equipamento->name','Equipamento','rigth');
        $column_quantidade = new TDataGridColumn('quantidade', 'Quantidade', 'right');
        $column_perda_id = new TDataGridColumn('Perda->descricao', 'Perda Id', 'right');
        $column_qtde_perda = new TDataGridColumn('qtde_perda', 'Qtde Perda', 'right');
        $column_dataAp = new TDataGridColumn('dataAp', 'Dataap', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_ordem_id);
        $this->datagrid->addColumn($column_equipamento_id);
        $this->datagrid->addColumn($column_quantidade);
        $this->datagrid->addColumn($column_perda_id);
        $this->datagrid->addColumn($column_qtde_perda);
        $this->datagrid->addColumn($column_dataAp);


        // creates the datagrid column actions
        $order_system_user_id = new TAction(array($this, 'onReload'));
        $order_system_user_id->setParameter('order', 'system_user_id');
        $column_system_user_id->setAction($order_system_user_id);
        
        $order_ordem_id = new TAction(array($this, 'onReload'));
        $order_ordem_id->setParameter('order', 'ordem_id');
        $column_ordem_id->setAction($order_ordem_id);
        
        $order_dataAp = new TAction(array($this, 'onReload'));
        $order_dataAp->setParameter('order', 'dataAp');
        $column_dataAp->setAction($order_dataAp);
        

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ApontamentoForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        
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
