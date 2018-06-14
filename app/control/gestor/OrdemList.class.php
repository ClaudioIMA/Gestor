<?php
/**
 * OrdemList Listing
 * @author  <your name here>
 */
class OrdemList extends TStandardList
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
        parent::setActiveRecord('Ordem');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('numero', 'like', 'numero'); // filterField, operator, formField
        parent::addFilterField('produto_id', 'like', 'produto_id'); // filterField, operator, formField
        parent::addFilterField('data_inicio', 'like', 'data_inicio'); // filterField, operator, formField
        parent::addFilterField('data_fim', 'like', 'data_fim'); // filterField, operator, formField
        parent::addFilterField('status', 'like', 'status'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Ordem');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Ordem');
        

        // create the form fields
        $id = new THidden('id');
        $numero = new TEntry('numero');
        $produto_id = new TEntry('produto_id');
        $data_inicio = new TEntry('data_inicio');
        $data_fim = new TEntry('data_fim');
        $status = new TEntry('status');


        // add the fields
        $this->form->addQuickField('Id', $id,  10 );
        $this->form->addQuickField('Numero', $numero,  200 );
        $this->form->addQuickField('Produto Id', $produto_id,  200 );
        $this->form->addQuickField('Data Inicio', $data_inicio,  200 );
        $this->form->addQuickField('Data Fim', $data_fim,  200 );
        $this->form->addQuickField('Status', $status,  200 );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Ordem_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('OrdemForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        
        // creates the datagrid columns
        $column_numero = new TDataGridColumn('numero', 'Numero', 'right');
        $column_eqpto_id = new TDataGridColumn('Equipamento->name', 'Equipamento', 'right');
        $column_produto_id = new TDataGridColumn('Produto->codigo', 'Produto', 'right');
        $column_qtde = new TDataGridColumn('qtde','Quantidade','right');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data Inicio', 'left');
        $column_data_fim = new TDataGridColumn('data_fim', 'Data Fim', 'left');
        $column_status = new TDataGridColumn('status', 'Status', 'left');
        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_numero);
        $this->datagrid->addColumn($column_eqpto_id);
        $this->datagrid->addColumn($column_produto_id);
        $this->datagrid->addColumn($column_qtde);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_fim);
        $this->datagrid->addColumn($column_status);
        
        
        // create EDIT action
        $action_edit = new TDataGridAction(array('OrdemForm', 'onEdit'));
        $action_edit->setUseButton(TRUE);
        $action_edit->setButtonClass('btn btn-default');
        $action_edit->setLabel(_t('Edit'));
        $action_edit->setImage('fa:pencil-square-o blue fa-lg');
        $action_edit->setField('id');
        $this->datagrid->addAction($action_edit);
        
        // create DELETE action
        $action_del = new TDataGridAction(array($this, 'onDelete'));
        $action_del->setUseButton(TRUE);
        $action_del->setButtonClass('btn btn-default');
        $action_del->setLabel(_t('Delete'));
        $action_del->setImage('fa:trash-o red fa-lg');
        $action_del->setField('id');
        $this->datagrid->addAction($action_del);
        
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
