<?php
require 'vendor/autoload.php';
define('DBS',array(

    'tecnologia' => array(
        'host'   => 'localhost',
        'user'   => 'root',
        'pass'   => '',
        'dbname' => 'tecnologia',
        'PREFIXTABLE' => 'tab'
    )

));
use Database\Record;
use Database\Transaction;

class Tecnologia extends Record
{
	const TABLENAME = 'tecnologia';
    const PRIMARYKEY = 'tec_id';

}

class Command extends Record
{
	const TABLENAME = 'command';
    const PRIMARYKEY = 'com_id';

    public function get_nome_tecnologia()
    {
    	return Tecnologia::find($this->com_idtec)->tec_name;
    }

}

if(isset($_GET['class']) && !empty($_GET['class']) ){
	switch($_GET['class'])
	{
		case 'command':
			try{
				Transaction::open();
				$command = new Command();
				$_POST = array_filter($_POST);
				$command->fromArray($_POST);
				$id = $command->store();
				
				Transaction::close();
				echo json_encode(['error' => false, 'response' => ['body' => $_POST, 'message' => 'Comando foi cadastrado com sucesso!'] ]);

			}catch(Exception $e){
				Transaction::rollback();
				echo json_encode(['error' => true, 'response' => [ 'message' => $e->getMessage()] ]);

			}
			exit;
		break;
		case 'tecnologia':

		break;
	}
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<meta charset="utf-8">
	<title>Comandos do git</title>
	<link rel="stylesheet" type="text/css" href="css/Table.css">
	<link rel="stylesheet" type="text/css" href="css/Boot.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<link rel="stylesheet" type="text/css" href="css/Alerts.css">
	<script type="text/javascript" src="js/main.js"></script>
	<script type="text/javascript" src="js/Alerts.js"></script>
</head>
<body>

	<form class="form" action="http://localhost/Git/projeto-git-17-08/?class=command" method="POST">
		<div class="input-group">
			<select class="item-select item" name="com_idtec">
				<?php 
				Transaction::open();
				$tecs = Tecnologia::all();		
				foreach($tecs as $tec):?>
					<option value="<?= $tec->tec_id;?>"><?= $tec->tec_name;?></option>
				<?php endforeach;?>
			
			</select>
			<input class="item-input item" type="text" name="com_command" placeholder="Comando">
			<textarea class="item-textarea item" name="com_desc" placeholder="Descrição do comando"></textarea>
		</div>
		<div class="input-group-submit">
			<input type="submit" value="Cadastrar!">
		</div>
	</form>

	<table class="table">
		<thead>
			<tr>
				<th>Tecnologia</th>
				<th>Comando</th>
				<th>Descrição</th>
			</tr>
		</thead>
		<tbody>
			<?php
			try{
			Transaction::open();

			$commands = Command::orderBy('com_command','asc')->load();
			foreach($commands as $command):
				$start = strrpos($command->com_desc,'#');
				$length = strlen($command->com_desc);
				$part = substr($command->com_desc,$start,$length-$start);
			?>
			<tr>
				<td><?= $command->nome_tecnologia;?></td>
				<td><?= $command->com_command;?></td>
				<td><?= str_replace($part, '<b>'.$part.'</b>', $command->com_desc);?></td>
			</tr>
			<?php
			endforeach;
			}catch(Exception $e){
				var_dump($e->getMessage());
			}
			
			

			?>
		</tbody>
	</table>

</body>
</html>