<?php
class ConsultaManual extends TPage
{

    public function __construct()
    {
    parent::__construct();
    
    try
        {
            // connection info
            $db = array();
            $db['host'] = '127.0.0.1';
            $db['port'] = '3306';
            $db['name'] = 'Gestor';
            $db['user'] = 'root';
            $db['pass'] = 'rnta55';
            $db['type'] = 'mysql';
            
            TTransaction::open(NULL, $db); // open transaction
            $conn = TTransaction::get(); // get PDO connection 
       
            $result=$conn->query('SELECT SUM(quantidade) as total, SUM(qtde_perda) as perda, gestor_ordem.numero FROM gestor_apontamento
INNER JOIN gestor_ordem ON ordem_id = gestor_ordem.id
GROUP BY gestor_ordem.numero'); 
        foreach ($result as $row)
        {   
        print "OS:". $row('numero');
        print " - PRODUZIDO:" . $row('total');
        print " - PERDA:". $row('perda')."</br>";
        
        }               
        TTransaction::close();
        }
        catch (Exception $e)
        {
        new TMessage('erro', $e->getMessage());
        }
    }
}
?>
