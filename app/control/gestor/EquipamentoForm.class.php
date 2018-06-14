<?php
/**
 * EquipamentoForm Registration
 * @author  Claudio Oliveira
 */
class EquipamentoForm extends TPage
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
        $this->form = new TQuickForm('form_Equipamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%;height:30%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Equipamento');
        


        // create the form fields
        $name = new TText('name');
        $setor = new TText('setor');
        $ativo = new TText('ativo');
        
        
        // add the fields
        $this->form->addQuickField('Nome', $name,  200, new TRequiredValidator);
        $this->form->addQuickField('Setor', $setor,  200 , new TRequiredValidator);
        $this->form->addQuickField('Ativo', $ativo,  2, new TRequiredValidator);
        $name->setSize(200,30);
        $setor->setSize(200,30);
        $ativo->setSize(200,30);

        
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
        $container->style = 'height: 40%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
    function onReload()
    {
        $this->form->clear();
    }
}
