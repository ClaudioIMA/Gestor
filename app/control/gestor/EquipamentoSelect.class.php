<?php
/**
 * EquipamentoSelect Registration
 * @author  <your name here>
 */
class EquipamentoSelect extends TPage
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
        
        if (!empty($_COOKIE['equipAtual']))
        {
            AdiantiCoreApplication::loadPage('ApontamentoForm');
        }
        
        $this->setDatabase('gestor');              // defines the database
        $this->setActiveRecord('Equipamento');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Equipamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Equipamento');
        


        // create the form fields
        $id = new THidden('id');
        $name = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $name->setChangeAction(new TAction(array($this,'onChangeCombo')));
        
        $id->setValue($name->id);
        // add the fields
        $this->form->addQuickField('Id', $id,  100 );
        $this->form->addQuickField('Name', $name,  200 );
        //$this->form->addQuickField('Ativo', $ativo,  200 );

        
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
        
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);
    }
 static function onChangeCombo($param)
    {
    
        $id = $param['eqpto_id'];
        $duracao = time() + (86400 * 180);
        setcookie('equipAtual',$id,$duracao);
        
    }
 
 public function onSave( $param )
    {
    
    AdiantiCoreApplication::loadPage('ApontamentoForm');  

    }     

}
