<?php
/**
 * PerdaList Listing
 * @author  <your name here>
 */
class PerdaList extends TStandardList
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
        parent::setActiveRecord('Perda');   // defines the active record
        parent::setDefaultOrder('id', 'asc');         // defines the default order
        // parent::setCriteria($criteria) // define a standard filter

        parent::addFilterField('codigo', 'like', 'codigo'); // filterField, operator, formField
        parent::addFilterField('descricao', 'like', 'descricao'); // filterField, operator, formField
        parent::addFilterField('message', 'like', 'message'); // filterField, operator, formField
        
        // creates the form
        $this->form = new TQuickForm('form_search_Perda');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Perda');
        

        // create the form fields
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $descricao = new TEntry('descricao');
        $message = new TEntry('message');


        // add the fields
        $this->form->addQuickField('Id', $id,  10 );
        $this->form->addQuickField('Codigo', $codigo,  200 );
        $this->form->addQuickField('Descricao', $descricao,  200 );
        $this->form->addQuickField('Message', $message,  200 );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Perda_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('PerdaForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a DataGrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_codigo = new TDataGridColumn('codigo', 'Codigo', 'right');
        $column_descricao = new TDataGridColumn('descricao', 'Descricao', 'left');
        $column_message = new TDataGridColumn('message', 'Message', 'left');
        $column_ativo = new TDataGridColumn('ativo', 'Ativo', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_codigo);
        $this->datagrid->addColumn($column_descricao);
        $this->datagrid->addColumn($column_message);
        $this->datagrid->addColumn($column_ativo);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('PerdaForm', 'onEdit'));
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
