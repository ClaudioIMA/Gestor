<?php
/**
 * PerdaForm Registration
 * @author  Claudio Oliveira
 */
class DefineEquipamento extends TPage
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
        $this->setActiveRecord('Equipamento');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Define');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Equipamento');
        


        // create the form fields
       
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');

        // add the fields
        
        $this->form->addQuickField('Equipamento', $eqpto_id,  100 , new TRequiredValidator); 
        $eqpto_id->setSize(200,30);


       
        
        /** samples
         $this->form->addQuickFields('Date', array($date1, new TLabel('to'), $date2)); // side by side fields
         $fieldX->addValidation( 'Field X', new TRequiredValidator ); // add validation
         $fieldX->setSize( 100, 40 ); // set size
         **/
         
        // create the form actions
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:floppy-o');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    function onSave()
    {       
            $this->form->validate(); // validate form data
            
            $object = new Equipamento;  // create an empty object
            $data = $this->form->getData(); // get form data as array
            $object->fromArray( (array) $data); // load the object with data
            setcookie('IdEquip', $object->id, (time() + (120 * 86400)),'/'); 
            new TMessage('info', TAdiantiCoreTranslator::translate('Record saved'));
             echo 'teste de cookie id: ' . $_COOKIE['IdEquip'];
            //AdiantiCoreApplication::loadPage('DiarioForm');
    }

}
