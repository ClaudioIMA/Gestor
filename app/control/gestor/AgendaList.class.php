<?php
/**
 * AgendaList Listing
 * @author  <your name here>
 */
class AgendaList extends TPage
{
    private $form; // form
    private $datagrid; // listing
    private $pageNavigation;
    private $formgrid;
    private $loaded;
    private $deleteButton;
    
    /**
     * Class constructor
     * Creates the page, the form and the listing
     */
    public function __construct()
    {
        parent::__construct();
        
        // creates the form
        $this->form = new TQuickForm('form_search_Agenda');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Agenda');
        

        // create the form fields
        $eqpto_id = new TEntry('eqpto_id');
        $ordem_id = new TEntry('ordem_id');
        $data_inicio = new TEntry('data_inicio');


        // add the fields
        $this->form->addQuickField('Eqpto Id', $eqpto_id,  200 );
        $this->form->addQuickField('Ordem Id', $ordem_id,  200 );
        $this->form->addQuickField('Data Inicio', $data_inicio,  200 );

        
        // keep the form filled during navigation with session data
        $this->form->setData( TSession::getValue('Agenda_filter_data') );
        
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onSearch')), 'fa:search');
        $this->form->addQuickAction(_t('New'),  new TAction(array('AgendaForm', 'onEdit')), 'bs:plus-sign green');
        
        // creates a Datagrid
        $this->datagrid = new TDataGrid;
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        // $this->datagrid->enablePopover('Popover', 'Hi <b> {name} </b>');
        

        // creates the datagrid columns
        $column_id = new TDataGridColumn('id', 'Id', 'right');
        $column_eqpto_id = new TDataGridColumn('Equipamento->name', 'Equipamento', 'right');
        $column_ordem_id = new TDataGridColumn('Ordem->numero', 'Ordem', 'right');
        $column_system_user_id = new TDataGridColumn('system_user->login', 'Usuario', 'right');
        $column_data_inicio = new TDataGridColumn('data_inicio', 'Data Inicio', 'left');
        $column_data_previsao = new TDataGridColumn('data_previsao', 'Data Previsao', 'left');


        // add the columns to the DataGrid
        $this->datagrid->addColumn($column_id);
        $this->datagrid->addColumn($column_eqpto_id);
        $this->datagrid->addColumn($column_ordem_id);
        $this->datagrid->addColumn($column_system_user_id);
        $this->datagrid->addColumn($column_data_inicio);
        $this->datagrid->addColumn($column_data_previsao);

        
        // create EDIT action
        $action_edit = new TDataGridAction(array('AgendaForm', 'onEdit'));
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
        
        // creates the page navigation
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
    
    /**
     * Inline record editing
     * @param $param Array containing:
     *              key: object ID value
     *              field name: object attribute to be updated
     *              value: new attribute content 
     */
    public function onInlineEdit($param)
    {
        try
        {
            // get the parameter $key
            $field = $param['field'];
            $key   = $param['key'];
            $value = $param['value'];
            
            TTransaction::open('gestor'); // open a transaction with database
            $object = new Agenda($key); // instantiates the Active Record
            $object->{$field} = $value;
            $object->store(); // update the object in the database
            TTransaction::close(); // close the transaction
            
            $this->onReload($param); // reload the listing
            new TMessage('info', "Record Updated");
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', '<b>Error</b> ' . $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    
    /**
     * Register the filter in the session
     */
    public function onSearch()
    {
        // get the search form data
        $data = $this->form->getData();
        
        // clear session filters
        TSession::setValue('AgendaList_filter_eqpto_id',   NULL);
        TSession::setValue('AgendaList_filter_ordem_id',   NULL);
        TSession::setValue('AgendaList_filter_data_inicio',   NULL);

        if (isset($data->eqpto_id) AND ($data->eqpto_id)) {
            $filter = new TFilter('eqpto_id', 'like', "%{$data->eqpto_id}%"); // create the filter
            TSession::setValue('AgendaList_filter_eqpto_id',   $filter); // stores the filter in the session
        }


        if (isset($data->ordem_id) AND ($data->ordem_id)) {
            $filter = new TFilter('ordem_id', 'like', "%{$data->ordem_id}%"); // create the filter
            TSession::setValue('AgendaList_filter_ordem_id',   $filter); // stores the filter in the session
        }


        if (isset($data->data_inicio) AND ($data->data_inicio)) {
            $filter = new TFilter('data_inicio', 'like', "%{$data->data_inicio}%"); // create the filter
            TSession::setValue('AgendaList_filter_data_inicio',   $filter); // stores the filter in the session
        }

        
        // fill the form with data again
        $this->form->setData($data);
        
        // keep the search data in the session
        TSession::setValue('Agenda_filter_data', $data);
        
        $param=array();
        $param['offset']    =0;
        $param['first_page']=1;
        $this->onReload($param);
    }
    
    /**
     * Load the datagrid with data
     */
    public function onReload($param = NULL)
    {
        try
        {
            // open a transaction with database 'gestor'
            TTransaction::open('gestor');
            
            // creates a repository for Agenda
            $repository = new TRepository('Agenda');
            $limit = 10;
            // creates a criteria
            $criteria = new TCriteria;
            
            // default order
            if (empty($param['order']))
            {
                $param['order'] = 'id';
                $param['direction'] = 'asc';
            }
            $criteria->setProperties($param); // order, offset
            $criteria->setProperty('limit', $limit);
            

            if (TSession::getValue('AgendaList_filter_eqpto_id')) {
                $criteria->add(TSession::getValue('AgendaList_filter_eqpto_id')); // add the session filter
            }


            if (TSession::getValue('AgendaList_filter_ordem_id')) {
                $criteria->add(TSession::getValue('AgendaList_filter_ordem_id')); // add the session filter
            }


            if (TSession::getValue('AgendaList_filter_data_inicio')) {
                $criteria->add(TSession::getValue('AgendaList_filter_data_inicio')); // add the session filter
            }

            
            // load the objects according to criteria
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                // iterate the collection of active records
                foreach ($objects as $object)
                {
                    // add the object inside the datagrid
                    $this->datagrid->addItem($object);
                }
            }
            
            // reset the criteria for record count
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            $this->pageNavigation->setCount($count); // count of records
            $this->pageNavigation->setProperties($param); // order, page
            $this->pageNavigation->setLimit($limit); // limit
            
            // close the transaction
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e) // in case of exception
        {
            // shows the exception error message
            new TMessage('error', $e->getMessage());
            // undo all pending operations
            TTransaction::rollback();
        }
    }
    
    /**
     * Ask before deletion
     */
    public function onDelete($param)
    {
        // define the delete action
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param); // pass the key parameter ahead
        
        // shows a dialog to the user
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    /**
     * Delete a record
     */
    public function Delete($param)
    {
        try
        {
            $key=$param['key']; // get the parameter $key
            TTransaction::open('gestor'); // open a transaction with database
            $object = new Agenda($key, FALSE); // instantiates the Active Record
            $object->delete(); // deletes the object from the database
            TTransaction::close(); // close the transaction
            $this->onReload( $param ); // reload the listing
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted')); // success message
        }
        catch (Exception $e) // in case of exception
        {
            new TMessage('error', $e->getMessage()); // shows the exception error message
            TTransaction::rollback(); // undo all pending operations
        }
    }
    



    
    /**
     * method show()
     * Shows the page
     */
    public function show()
    {
        // check if the datagrid is already loaded
        if (!$this->loaded AND (!isset($_GET['method']) OR !(in_array($_GET['method'],  array('onReload', 'onSearch')))) )
        {
            if (func_num_args() > 0)
            {
                $this->onReload( func_get_arg(0) );
            }
            else
            {
                $this->onReload();
            }
        }
        parent::show();
    }
}
