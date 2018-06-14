<?php
/**
 * OrdemForm Registration
 * @author  <your name here>
 */
class OrdemEditForm extends TPage
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
        $this->setActiveRecord('Ordem');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Ordem');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Ordem');
        


        // create the form fields
        $id = new THidden('id');
        $numero = new TEntry('numero');
        $produto_id = new TDBCombo('produto_id','gestor','Produto','id','descricao');
        $data_inicio = new TDateTime('data_inicio');
        $qtde = new TEntry('qtde');
        
        $data_inicio->setMask('dd/mm/yyyy hh:ii');
        $data_inicio->setDatabaseMask('yyyy-mm-dd hh:ii');


        // add the fields
        $this->form->addQuickField('Id',$id,20);
        $this->form->addQuickField('Numero', $numero,  100 , new TRequiredValidator);
        $this->form->addQuickField('Produto Id', $produto_id,  100 , new TRequiredValidator);
        $this->form->addQuickField('Data Inicio', $data_inicio,  200 , new TRequiredValidator);
        $this->form->addQuickField('Quantidade', $qtde,  100 , new TRequiredValidator);


        
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
        $this->form->addQuickAction(_t('New'),  new TAction(array($this, 'onEdit')), 'bs:plus-sign green');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
}
