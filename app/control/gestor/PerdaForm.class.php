<?php
/**
 * PerdaForm Registration
 * @author  Claudio Oliveira
 */
class PerdaForm extends TPage
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
        $this->setActiveRecord('Perda');     // defines the active record
        
        // creates the form
        $this->form = new TQuickForm('form_Perda');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Perda');
        


        // create the form fields
        $id = new THidden('id');
        $codigo = new TEntry('codigo');
        $descricao = new TText('descricao');
        $message = new TText('message');
        //$eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');
        $ativo = new THidden('ativo');

        // add the fields
        $this->form->addQuickField('Id',$id,20);
        $this->form->addQuickField('Codigo', $codigo,  100 , new TRequiredValidator);
        $this->form->addQuickField('Descricao', $descricao,  200 , new TRequiredValidator);
        $this->form->addQuickField('Message', $message,  200 , new TRequiredValidator);
        //$this->form->addQuickField('Equipamento', $eqpto_id,  100 , new TRequiredValidator);
        $this->form->addQuickField('Ativo', $ativo,  20 , new TRequiredValidator);
        $codigo->setSize(50,30);
        $descricao->setSize(200,30);
        $message->setSize(200,60);
        //$eqpto_id->setSize(200,30);
        $ativo->setSize(20,30);


        
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
