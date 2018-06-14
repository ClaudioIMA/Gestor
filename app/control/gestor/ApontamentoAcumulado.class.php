<?php
/**
 * ApontamentoList Listing
 * @author  <your name here>
 */
class ApontamentoAcumulado extends TPage
{
    protected $form;     // registration form
    protected $datagrid; // listing
 
    /**
     * Page constructor
     */
    public function __construct()
    {
        parent::__construct();
        

        $this->form = new TQuickForm('form_search_Apontamento');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        $this->form->setFormTitle('Acumulado');
        $this->datagrid = new TDataGrid;

        //Cria as colunas do Datagrid
        $date       = new TDataGridColumn('date',    'Data',    'left',   100);
        $produzido       = new TDataGridColumn('produzido',    'Produzido',    'left',   80);
        $perda    = new TDataGridColumn('perda', 'Perda', 'left',   80);
        $perdapercentual  = new TDataGridColumn('perdapercentual',  'Perda %',   'left',   60);
        
        // add the columns to the datagrid
        $this->datagrid->addColumn($date);
        $this->datagrid->addColumn($produzido);
        $this->datagrid->addColumn($perda);
        $this->datagrid->addColumn($perdapercentual);
        
        //$this->datagrid->addQuickColumn('Data', 'data', 'left', '40%');
        //$this->datagrid->addQuickColumn('Produzido', 'produzido',    'left', '20%');
        //$this->datagrid->addQuickColumn('Perda', 'perda',    'left', '20%');
        //$this->datagrid->addQuickColumn('%Perda', 'perdapercentual', 'left', '20%');   

        $this->datagrid->createModel();
        // Cria os campos de pesquisa
        $dataini = new TDate('dataini');
        $datafim = new TDate('datafim');
        $eqpto_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');

        // adiciona os campos ao Form
        $this->form->addQuickField('Data Inicial', $dataini,  200 );
        $this->form->addQuickField('Data Final', $datafim,  200 );
        $this->form->addQuickField('Equipamento', $eqpto_id,  200 );

    
        // add the search form actions
        $this->form->addQuickAction(_t('Find'), new TAction(array($this, 'onReload')), 'fa:search');
        
        
        
        // create the page navigation
        


        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($this->form);
        $container->add($this->datagrid);
        
        parent::add($container);
    }
    
    function onReload($param)
    {
    
    
       $this->datagrid->clear();
        
        if (!empty($param['dataini']))
        {
        $datainicial = $param['dataini'];
        }else{
        $primeirodia = strtotime("first day of this month");
        $datainicial = date('Y-m-d',$primeirodia);
        }
        
        if (!empty($param['datafim']))
        {
        $datafinal = $param['datafim'];
        }else{
        $ultimodia = strtotime("last day of this month");
        $datafinal = date('Y-m-d',$ultimodia);
        }
        
        
        $this->datagrid->style = 'width: 100%';
        $this->datagrid->datatable = 'true';
        
  
        
        try
            {
            TTransaction::open('gestor');
            $conn = TTransaction::get(); // get PDO connection 
       
                if (empty($param['eqpto_id']))
                {
                $result=$conn->query("SELECT SUM(quantidade) as total, SUM(qtde_perda) as perda,
                DATE_FORMAT(dataAp, '%Y-%m-%d') as dia FROM gestor_apontamento WHERE dataAP BETWEEN '".
                $datainicial . "' AND '" . $datafinal . "' GROUP BY dia ORDER BY dia ASC ");
                }else{
                $result=$conn->query("SELECT SUM(quantidade) as total, SUM(qtde_perda) as perda,
                DATE_FORMAT(dataAp, '%Y-%m-%d') as dia FROM gestor_apontamento WHERE dataAP BETWEEN '".
                $datainicial . "' AND '" . $datafinal . "' AND eqpto_id = ".$param['eqpto_id']. " GROUP BY dia ORDER BY dia ASC "); 
                }
                $acumulado = 0;
                $perdatotal = 0;
                foreach ($result as $row)
                {
                // add the columns to the DataGrid
                $item = new StdClass;
                $item->produzido= number_format($row['total'],0, ',', '.');
                $item->perda     = number_format($row['perda'],0, ',', '.');
                $item->date  = $row['dia'];
                $item->perdapercentual  = number_format((($row['perda']/$row['total'])*100),2,',','.');
                $this->datagrid->addItem($item);
                $acumulado += $row['total'];
                $perdatotal += $row['perda'];
                }
            $item = new StdClass;
            $item->produzido= number_format($acumulado,0, ',', '.');
            $item->perda     = number_format($perdatotal,0, ',', '.');
            $item->date  = 'TotalAcumulado';
            $item->perdapercentual  = number_format(($perdatotal/$acumulado*100),2,',','.');
            $this->datagrid->addItem($item);
                                       
            TTransaction::close();
        }
        catch (Exception $e)
            {
            new TMessage('Erro', $e->getMessage());
            }
    
    }
    
    function show()
    {
        $this->onReload(null);
        parent::show();
    }
}
