<?php
session_start();
include("config.php");
$id = $_SESSION["id"];

$busca_fornnecedores = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT * FROM fornecedores WHERE id = '{$id}'"));
?>

<html lang="en">
	<!--begin::Head-->
	<head>
		<base href="">
		<title>LSC</title>
		<meta charset="utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<meta property="og:locale" content="pt-br" />
		<meta property="og:type" content="" />
		<meta property="og:title" content="" />
		<meta property="og:url" content="" />
		<meta property="og:site_name" content="" />
		<link rel="canonical" href="" />
		<link rel="shortcut icon" href="../assets/media/misc/LSC-icone.png" />

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	</head>

<div class="modal-header">
	<h5 class="modal-title" id="exampleModalLabel"><?php echo $busca_fornnecedores["razao"]; ?></h5>
	<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
  </div>
<div class="modal-body" >
	<form method="post" action="index.php?a=alterarDados&id=<?php echo $id; ?>" enctype="multipart/form-data">
  		<div class="form-group d-flex flex-column mb-8">
			<label class="fs-6 fw-bold mb-2" for="titulo">Representante:</label>
			<input type="text" class="form-control form-control-solid" name="representante" value="<?php echo $busca_fornnecedores["representante"]; ?>">
		</div>
		
		<div class="form-group d-flex flex-column mb-8">
			<label class="fs-6 fw-bold mb-2" for="titulo">CPF:</label>
			<input type="text" class="form-control form-control-solid" name="cpf" value="<?php echo $busca_fornnecedores["cpf"]; ?>">
		</div>
		
		<div class="form-group d-flex flex-column mb-8">
			<label class="fs-6 fw-bold mb-2" for="titulo">Telefone:</label>
			<input type="text" class="form-control form-control-solid" name="telefone" value="<?php echo $busca_fornnecedores["telefone"]; ?>">
		</div>
		
		<div class="form-group d-flex flex-column mb-8">
			<label class="fs-6 fw-bold mb-2" for="titulo">E-mail:</label>
			<input type="text" class="form-control form-control-solid" name="email" value="<?php echo $busca_fornnecedores["email"]; ?>">
		</div>
		
		<div class="row">
			<div class="col-md-9 col-9">
				<div class="form-group d-flex flex-column mb-8">
					<label class="fs-6 fw-bold mb-2" for="titulo">Rua:</label>
					<input type="text" class="form-control form-control-solid" name="rua" value="<?php echo $busca_fornnecedores["rua"]; ?>">
				</div>
			</div>
			<div class="col-md-3 col-3">
				<div class="form-group d-flex flex-column mb-8">
					<label class="fs-6 fw-bold mb-2" for="titulo">N:</label>
					<input type="text" class="form-control form-control-solid" name="numero" value="<?php echo $busca_fornnecedores["numero"]; ?>">
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-md-4">
				<div class="form-group d-flex flex-column mb-8">
					<label class="fs-6 fw-bold mb-2" for="titulo">Bairro:</label>
					<input type="text" class="form-control form-control-solid" name="bairro" value="<?php echo $busca_fornnecedores["bairro"]; ?>">
				</div>
			</div>
			<div class="col-md-5">
				<div class="form-group d-flex flex-column mb-8">
					<label class="fs-6 fw-bold mb-2" for="titulo">Cidade:</label>
					<input type="text" class="form-control form-control-solid" name="cidade" value="<?php echo utf8_encode($busca_fornnecedores["cidade"]); ?>">
				</div>
			</div>
			<div class="col-md-3">
				<div class="form-group d-flex flex-column mb-8">
					<label class="fs-6 fw-bold mb-2" for="titulo">UF:</label>
					<input type="text" class="form-control form-control-solid" name="estado" value="<?php echo $busca_fornnecedores["estado"]; ?>">
				</div>
			</div>
		</div>
		

		<div class="form-group d-flex flex-column mb-8">
			<button type="submit" class="btn btn-danger">Salvar</button>
		</div>


	</form>    
</div>
</html>