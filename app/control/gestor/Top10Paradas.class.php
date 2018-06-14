<?php

class Top10Paradas extends TPage
{
    /**
     * Class constructor
     * Creates the page
     */
    function __construct()
    {
        parent::__construct();
        
        $this->form = new TQuickForm('form_Ordem_report');
        $this->form->class = 'tform'; // change CSS class
        
        $this->form->style = 'display: table;width:100%'; // change style
        
        // define the form title
        $this->form->setFormTitle('Top 10 Paradas - Selecione Periodo');
        


        // create the form fields
        $data_inicio = new TDate('data_inicio');
        $data_fim = new TDate('data_fim');
        $equipamento_id = new TDBCombo('eqpto_id','gestor','Equipamento','id','name');

        // add the fields
        $this->form->addQuickField('Data Inicio', $data_inicio,  100);
        $this->form->addQuickField('Data Fim', $data_fim,  100 );
        $this->form->addQuickField('Equipamento',$equipamento_id,100);
        
        $this->form->addQuickAction(_t('Generate'), new TAction(array($this, 'onGenerate')), 'fa:cog blue');
        
        // vertical box container
        $container = new TVBox;
        $container->style = 'width: 90%';
        // $container->add(new TXMLBreadCrumb('menu.xml', __CLASS__));
        $container->add($this->form);
        
        parent::add($container);       
        
        
    }
    function onGenerate($param)
    {    
       if (!empty($param['data_inicio']))
        {
        $datainicial = $param['data_inicio'];
        }else{
        $primeirodia = strtotime("first day of this month");
        $datainicial = date('Y-m-d',$primeirodia);
        }
        
        if (!empty($param['data_fim']))
        {
        $datafinal = $param['data_fim'];
        }else{
        $ultimodia = strtotime("last day of this month");
        $datafinal = date('Y-m-d',$ultimodia);
        }
      
      $data1 = array();
      $data1[0]= ('Paradas entre:'.$datainicial. ' e '.$datafinal);
      $data2 = array();
      $data2[0]=('Minutos');
      $i=0;
      
         try
                {
                TTransaction::open('gestor');
                $conn = TTransaction::get(); // get PDO connection 
            if (empty($param['eqpto_id']))
            {
            $result=$conn->query("SELECT SUM(minutos) as tempo, causa FROM (SELECT TIMESTAMPDIFF(MINUTE,data_inicio,data_fim) as minutos, 
            gestor_parada.descricao as causa FROM (SELECT parada_id, data_inicio, data_fim FROM gestor_diario 
            WHERE data_inicio BETWEEN '".$datainicial. " 00:00:00' AND '".$datafinal." 23:59:59') as subPeriodo
INNER JOIN gestor_parada ON parada_id = gestor_parada.id ORDER BY minutos DESC) as resumo GROUP BY causa ORDER BY tempo ASC LIMIT 10");
            }else{
             $result=$conn->query("SELECT SUM(minutos) as tempo, causa FROM (SELECT TIMESTAMPDIFF(MINUTE,data_inicio,data_fim) as minutos, 
            gestor_parada.descricao as causa FROM (SELECT parada_id, data_inicio, data_fim FROM gestor_diario 
            WHERE data_inicio BETWEEN '".$datainicial. " 00:00:00' AND '".$datafinal." 23:59:59' AND eqpto_id =".$param['eqpto_id']. ") as subPeriodo
INNER JOIN gestor_parada ON parada_id = gestor_parada.id ORDER BY minutos DESC) as resumo GROUP BY causa ORDER BY tempo ASC LIMIT 10");
            }
           
                    foreach ($result as $row)
                    {
                     $i = $i + 1 ;
                     //$causa = $row['descricao'];
                     //$valor = $row['perda'];  
                     $data1[$i]=($row['causa']);
                     $data2[$i]=($row['tempo']); 
                    
                    }
                TTransaction::close();
                $datafinal = [$data1,$data2];
                }
        catch (Exception $e)
                {
                new TMessage('Erro', $e->getMessage());
                }
        
        $html = new THtmlRenderer('app/resources/google_bar_chart.html');
        $data = array();
        
        $panel = new TPanelGroup('Top 10 Paradas');
        $panel->add($html);
        
        // replace the main section variables
        $html->enableSection('main', array('data'   => json_encode($datafinal),
                                           'width'  => '100%',
                                           'height'  => '400px',
                                           'title'  => 'Motivos de Parada',
                                           'ytitle' => 'Minutos', 
                                           'xtitle' => ''));
        
        // add the template to the page
        $container = new TVBox;
        $container->style = 'width: 90%';
        $container->add($panel);
        parent::add($container);
    }
}
