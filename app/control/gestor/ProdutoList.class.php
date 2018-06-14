<?php
/**
 * ProdutoList Listing
 * @author  <your name here>
 */
class ProdutoList extends TStandardList
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
        parent::setActiveRecord('Produto');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('codigo', 'like', 'codigo'); // filterField, operator, formField
        parent::addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        parent::addFilterField('prod_hora', 'like', 'prod_hora'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Produto');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Produto');
        

        // create the form fields
        $codigo = new TEntry('codigo');
        $descricao = new TEntry('descricao');
        $prod_hora = new TEntry('prod_hora');


        // add the fields
        $this->form->addQuickField('Codigo', $codigo,  200 );
        $this->form->addQuickField('Descricao', $descricao,  200 );
        $this->form->addQuickField('Prod Hora', $prod_hora,  200 );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Produto_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('ProdutoForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_codigo = new TDataGridColumn('codigo', 'Codigo', 'left');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_prod_hora = new TDataGridColumn('prod_hora', 'Prod Hora', 'right');
        $column_ciclo = new TDataGridColumn('ciclo', 'Ciclo', 'right');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_prod_hora);
        $this->datagrid->addColumn($column_ciclo);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('ProdutoForm', 'onEdit'));
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
