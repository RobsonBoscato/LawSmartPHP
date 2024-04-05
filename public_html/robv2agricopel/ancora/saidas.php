<?php
include("../controle_sessao.php");
include("../config.php");
    $acao = mysqli_escape_string($lawsmt, $_GET['a']);
	if($acao == 'confirmar') {
		$id = mysqli_escape_string($lawsmt, $_GET['id']);
		// echo "SELECT * FROM operacoes WHERE id = $id";
 		$query = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT * FROM operacoes WHERE id = '{$id}'"));
		
		// echo "OPERACAO IS".$operacao;
		// echo "QUERY IS".$query;
		if($query['confirmada'] == '1') {
			$status = 0;
		} else {
			$status = 1;
		}
		// echo "UPDATE operacoes SET confirmada = '{$status}' WHERE id in ('{$id}')";
		$query = mysqli_query($lawsmt, "UPDATE operacoes SET confirmada = '{$status}' WHERE id in ({$id})");
		require_once('/home/lawsmart/send_mail.php');
		$operacao = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT *, antecipadas.id as oper_id, operacoes.dataOPE as data_oper, antecipadasDetalhes.valor as valor_antecip, antecipadas.valor as valor_antecip_liq FROM antecipadasDetalhes 
		inner join boletos on boletos.operacao = antecipadasDetalhes.operacao
		inner join operacoes on operacoes.id = antecipadasDetalhes.operacao
		inner join antecipadas on antecipadas.id = antecipadasDetalhes.antecipada
		inner join fornecedores on fornecedores.cnpj = operacoes.cnpj 
		WHERE antecipadasDetalhes.operacao in ({$id})"));
		
		if (is_null($operacao['email'])) {
			$operacao = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT * FROM operacoes inner join fornecedores on fornecedores.cnpj like operacoes.cnpj WHERE operacoes.id = '{$id}'"));
			// echo "DISPTCHING".var_dump($operacao);
			$operacao["fornecedor"] = $operacao["razao"];
			// $operacao['email']
			// dispatch_event_mail('postergacao_paga', $operacao['email'], $operacao["razao"], $operacao);
		} else {

			// echo "DISPTCHING ANTEC";
			$operacao["fornecedor"] = $operacao["razao"];
			dispatch_event_mail('antecipacao_paga', $operacao['email'], $operacao["razao"], $operacao);
		}
		echo "<script>alert('Operação confirmada com sucesso.');</script>";
  		
  		// $fornecedor[0]["email"]
	}
?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="">
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
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="white-translucent">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="white" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-title" content="LAW SMART">
		<link rel="shortcut icon" sizes="16x16" href="../assets/media/misc/LSC-icone.png">
		<link rel="shortcut icon" sizes="196x196" href="../assets/media/misc/LSC-icone.png">
		<link rel="apple-touch-icon-precomposed" href="../assets/media/misc/LSC-icone.png">

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="../assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="../assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="../assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		
		<link rel="apple-touch-icon" sizes="180x180" href="../assets/media/misc/LSC-icone.png">
		<link rel="stylesheet" type="text/css" href="../addtohomescreen.css">
		<script src="../addtohomescreen.js"></script>

	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" style="background-image: url('../assets/media/misc/page-bg-dark.jpg')" class="dark-mode page-bg header-fixed header-tablet-and-mobile-fixed aside-enabled">
		<!--begin::Main-->
		<!--begin::Root-->
		<div class="d-flex flex-column flex-root">
			<!--begin::Page-->
			<div class="page d-flex flex-row flex-column-fluid">
				<!--begin::Wrapper-->
				<div class="wrapper d-flex flex-column flex-row-fluid" id="kt_wrapper">
					<!--begin::Header-->
					<?php include("top.php"); ?>
					<!--end::Header-->
					<!--begin::Container-->
					<div id="kt_content_container" class="d-flex flex-column-fluid align-items-start container-xxl">
						<!--begin::Aside-->
						<?php include("side.php"); ?>
						<!--end::Aside-->
						<!--begin::Post-->
						<div class="content flex-row-fluid" id="kt_content">
							<!--begin::Row-->
							
							
							
							
							<div class="row g-5 g-xl-8">
								<div class="col-xl-12">
									
									
									<div class="row g-5 g-xl-8">
										<div class="col-xl-6">
											<!--begin::Statistics Widget 5-->
											<a href="#" class="card bg-info hoverable card-xl-stretch mb-xl-8">
												<!--begin::Body-->
												<div class="card-body">
													<!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
													<span class="svg-icon svg-icon-white svg-icon-3x ms-n1">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
															<rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
															<rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
															<rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="text-white-900 fw-bolder fs-2 mb-2 mt-5">R$ 0,00</div>
													<div class="fw-bold text-white-400">Entradas para Hoje</div>
												</div>
												<!--end::Body-->
											</a>
											<!--end::Statistics Widget 5-->
										</div>
										
										
										<div class="col-xl-6">
											<!--begin::Statistics Widget 5-->
											<a href="#" class="card bg-danger hoverable card-xl-stretch mb-5 mb-xl-8">
												<!--begin::Body-->
												<div class="card-body">
													<!--begin::Svg Icon | path: icons/duotune/graphs/gra007.svg-->
													<span class="svg-icon svg-icon-white svg-icon-3x ms-n1">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<path opacity="0.3" d="M10.9607 12.9128H18.8607C19.4607 12.9128 19.9607 13.4128 19.8607 14.0128C19.2607 19.0128 14.4607 22.7128 9.26068 21.7128C5.66068 21.0128 2.86071 18.2128 2.16071 14.6128C1.16071 9.31284 4.96069 4.61281 9.86069 4.01281C10.4607 3.91281 10.9607 4.41281 10.9607 5.01281V12.9128Z" fill="black"></path>
															<path d="M12.9607 10.9128V3.01281C12.9607 2.41281 13.4607 1.91281 14.0607 2.01281C16.0607 2.21281 17.8607 3.11284 19.2607 4.61284C20.6607 6.01284 21.5607 7.91285 21.8607 9.81285C21.9607 10.4129 21.4607 10.9128 20.8607 10.9128H12.9607Z" fill="black"></path>
														</svg>
													</span>
													<?php
														$date = date('Y-m-d');
														$totaloperado = mysqli_fetch_assoc(mysqli_query($lawsmt, "select distinct COALESCE(SUM(antecipadasDetalhes.valor),0)+COALESCE(SUM((select oper.valor from operacoes oper where oper.id = operacoes.id and operacoes.status = 4)),0) AS valor from operacoes left join antecipadasDetalhes on antecipadasDetalhes.operacao = operacoes.id where (operacoes.status = 4 and operacoes.vencimento = '{$date}') or (operacoes.id = antecipadasDetalhes.operacao and operacoes.status = 5 and operacoes.dataOPE = '{$date}')")) or die(mysqli_error());
														$conta_opera = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaloperacoes FROM operacoes")) or die(mysqli_error());
													?>
													<div class="text-white fw-bolder fs-2 mb-2 mt-5">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></div>
													<div class="fw-bold text-white">Saidas Para hoje</div>
												</div>
												<!--end::Body-->
											</a>
											<!--end::Statistics Widget 5-->
										</div>
									</div>
								</div>
								
							</div>
							
							
							
							<div class="row gy-5 g-xl-8">
								<!--begin::Col-->
								<div class="col-xl-12">
									<div class="col-xl-12">
									<div class="card mb-8 mb-xl-10">
										<div class="card-header border-0 pt-10">
											<h3 class="card-title align-items-start flex-column">
												<span class="card-label fw-bolder fs-3 mb-1">Fluxo do Periodo</span>
												<span class="text-muted mt-1 fw-bold fs-7">Abaixo listamos as todos fornecedores disponiveis no sistema.</span>
											</h3>
											<?php
												$inicio = mysqli_escape_string($lawsmt, $_GET["inicio"]);
												$termino = mysqli_escape_string($lawsmt, $_GET["termino"]);
												$status = mysqli_escape_string($lawsmt, $_GET["status"]);
												$tipo = mysqli_escape_string($lawsmt, $_GET["tipo"]);
											?>
											<form name="filtro" method="get" style="width: 100%;" action="saidas.php">
												<div class="row">
													<!-- <div class="col-md-3">
														<select name="tipo" class="form-control">
															<option value="">Todas</option>
															<option value="saida" <?php if($tipo == "saida") { echo "selected"; } ?>>Saída</option>
															<option value="entrada" <?php if($tipo == "entrada") { echo "selected"; } ?>>Entrada</option>
														</select>
													</div> -->
													<div class="col-md-3">
														<select name="status" class="form-control">
															<option value="">Todas</option>
															<option value="4" <?php if($status == "4") { echo "selected"; } ?>>Postergado</option>
															<option value="1" <?php if($status == "1") { echo "selected"; } ?>>Antecipado</option>
														</select>
													</div>
													
													<div class="col-md-2">
														<input type="date" name="inicio" class="form-control" value="<?php echo $inicio; ?>">
													</div>
													<div class="col-md-2">
														<input type="date" name="termino" class="form-control" value="<?php echo $termino; ?>">
													</div>
													<div class="col-md-1">
														<input type="submit" name="envio" class="form-control" value="Filtrar">
													</div>
												</div>
											</form>
										</div>
										<div class="card-body py-3">
											<!--begin::Table container-->
											<div class="table-responsive">
												<!--begin::Table-->
												<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3" id="fluxo_table">
													<!--begin::Table head-->
													<thead>
														<tr class="fw-bolder text-muted">
															<th class="min-w-200px">Cliente</th>
															<th class="min-w-100px">Transação Id</th>
															<th class="min-w-120px">Data</th>
															<th class="min-w-80px">Valor</th>
															<th class="min-w-120px">Tipo</th>
															<th class="min-w-120px">Pag</th>
														</tr>
													</thead>
													<tbody>
														<?php
															$query = "select distinct (select razao from fornecedores 
															where cnpj like operacoes.cnpj limit 1 ) as razao, operacoes.cnpj as cnpj, operacoes.tipo as tipo, antecipadasDetalhes.antecipada as antec_id, IFNULL(antecipadasDetalhes.antecipada,UUID()) as antec_id_uniq, 
															GROUP_CONCAT(operacoes.id) as id_oper, vencimento, sum(operacoes.valor)  as valor, 
															operacoes.status as status, 
															antecipadasDetalhes.data as data_antec, 
															antecipadas.valor as valor_antec from operacoes

															left join postergacoesDetalhes on postergacoesDetalhes.id_operacao = operacoes.id or postergacoesDetalhes.id_postergada = operacoes.id

															left join antecipadasDetalhes 
															on antecipadasDetalhes.operacao = operacoes.id  
															and antecipadasDetalhes.antecipada is not null  


															left join antecipadas on antecipadas.id = antecipadasDetalhes.antecipada
															where operacoes.status not in (0, 1, 6)
                                                             and (operacoes.id != postergacoesDetalhes.id_operacao or postergacoesDetalhes.id is null)
															 and operacoes.confirmada = 0
															
															";
															if($inicio) {
																$query .= " and vencimento >= '{$inicio}' and operacoes.dataOPE >= '{$inicio}'";
																// die($query);
															}

															if($termino) {
																$query .= " and vencimento <= '{$termino}' and operacoes.dataOPE <= '{$termino}'";
															}

															if ($tipo) {
																if($tipo == 'saida') {
																	$query .= " and (operacoes.id != postergacoesDetalhes.id_operacao or postergacoesDetalhes.id is null) ";
																} else {
																	$query .= " and operacoes.status not in (1, 4)";
																}
															}

															if($status) {
																if ($status == "4") {
																	$query .= " AND operacoes.status in (4, 5) and antecipadasDetalhes.antecipada is null and (select status from operacoes oper where oper.id  = postergacoesDetalhes.id_operacao) = 5";	
																	// echo $query;
																} else {
																	$query .= " AND operacoes.status in (1, 5) and antecipadasDetalhes.antecipada is not null and (select status from operacoes oper where oper.id = antecipadasDetalhes.operacao) = 5";	
																}
															}  else {
																$query .= " AND (operacoes.status in (4, 5) and antecipadasDetalhes.antecipada is null and (select status from operacoes oper where oper.id  = postergacoesDetalhes.id_operacao ) = 5) or (operacoes.status in (1, 5) and antecipadasDetalhes.antecipada is not null and (select status from operacoes oper where oper.id = antecipadasDetalhes.operacao) = 5)";
															}
															
															// $query .= " and operacoes.confirmada = 1";
                                                            $query .= " and operacoes.confirmada = 0 ";
                                                            
															$query .= " group by antec_id_uniq order by operacoes.vencimento";

															// echo $query;
															$antecipadas = mysqli_query($lawsmt, $query);
															// echo "<br>";
															// echo var_dump($antecipadas);
   															while($row = mysqli_fetch_assoc($antecipadas)) {
																if (is_null($row['valor_antec']) == false) {
																	$data_antec = new DateTime($row['data_antec']); 
																	$antecid = $row['antec_id'];
																	echo '<tr>
																	<td>
																		<a href="#" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6">'.$row["razao"].'</a>
																		<span class="text-muted fw-bold text-muted d-block fs-7">'.$row["cnpj"].'</span>
																	</td>
																	<td>
																		<a href="operacao.php?id='.$antecid.'" class="text-dark fw-bolder text-hover-primary fs-6">0000'.$antecid.'</a>
																	</td>
																	
																	<td>
 																		<div class="fs-6 text-gray-800 fw-bolder">'.date_format(date_create($operac['data_antec']), 'd-M-Y').'</div>
																	</td>
																	<td>
																		<div class="fs-6 text-gray-800 fw-bolder">R$ '.$row['valor_antec'].'</div>
																	</td>
																	<td>
																		<span class="badge badge-light-danger">Antecipação</span>
																	</td>
																	<td>
                                                                        <a href="saidas.php?id='.$row['id_oper'].'&a=confirmar">
                                                                            <button data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="right" title="Informar Pagamento" class="btn btn-active-icon-primary btn-active-text-primary" style="margin:0; padding:0;" id="efetuar-antecipadas">
                                                                            <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr016.svg-->
                                                                            <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                                <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="black"/>
                                                                                <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="black"/>
                                                                                </svg>
                                                                            </span>
                                                                            <!--end::Svg Icon-->
                                                                            </button>
                                                                       </a>
																	</td>
																</tr>';
																continue 1;
															}
																
														?>
														<tr>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6"><?php echo $row["razao"]; ?></a>
																<span class="text-muted fw-bold text-muted d-block fs-7"><?php echo $row["cnpj"]; ?></span>
															</td>
															<td>
																<a href="operacao.php?id=<?php 
																if(is_null($row['antec_id'])) {
																	echo $row['id_oper']; 
																} else {
																	echo $row['antec_id']; 
																}?>" class="text-dark fw-bolder text-hover-primary fs-6">0000<?php if(is_null($row['antec_id'])) {
																	echo $row['id_oper']; 
																} else {
																	echo $row['antec_id']; 
																}?></a>
															</td>
															
															<td>
																<div class="fs-6 text-gray-800 fw-bolder"><?php $data = new DateTime($row['vencimento']); echo $data->format('d-M-Y'); ?></div>
																<!-- <span class="text-muted fw-bold text-muted d-block fs-7">link para nota</span> -->
															</td>
															<td>
																<div class="fs-6 text-gray-800 fw-bolder">R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></div>
															</td>
															<td>
																<?php if(($row["tipo"] == 'cliente' || is_null($row["valor_antec"]) == false) && $row['status'] != '4') { ?>
																	<span class="badge badge-light-success"> Entrada </span>
																<?php
																} else { ?>
																	<span class="badge badge-light-danger"> Postergação </span>
																<?php
																}?>
															</td>
															<td>
																<span>
                                                                    <a href="saidas.php?id=<?php echo $row['id_oper'] ?>&a=confirmar">
                                                                        <button data-bs-toggle="tooltip" data-bs-custom-class="tooltip-inverse" data-bs-placement="right" title="Informar Pagamento" class="btn btn-active-icon-primary btn-active-text-primary" style="margin:0; padding:0;" id="efetuar-antecipadas">
                                                                        <!--begin::Svg Icon | path: assets/media/icons/duotune/arrows/arr016.svg-->
                                                                        <span class="svg-icon svg-icon-muted svg-icon-2hx"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                                            <path opacity="0.3" d="M10.3 14.3L11 13.6L7.70002 10.3C7.30002 9.9 6.7 9.9 6.3 10.3C5.9 10.7 5.9 11.3 6.3 11.7L10.3 15.7C9.9 15.3 9.9 14.7 10.3 14.3Z" fill="black"/>
                                                                            <path d="M22 12C22 17.5 17.5 22 12 22C6.5 22 2 17.5 2 12C2 6.5 6.5 2 12 2C17.5 2 22 6.5 22 12ZM11.7 15.7L17.7 9.70001C18.1 9.30001 18.1 8.69999 17.7 8.29999C17.3 7.89999 16.7 7.89999 16.3 8.29999L11 13.6L7.70001 10.3C7.30001 9.89999 6.69999 9.89999 6.29999 10.3C5.89999 10.7 5.89999 11.3 6.29999 11.7L10.3 15.7C10.5 15.9 10.8 16 11 16C11.2 16 11.5 15.9 11.7 15.7Z" fill="black"/>
                                                                            </svg>
                                                                        </span>
                                                                        <!--end::Svg Icon-->
                                                                        </button>
                                                                    </a>
                                                                </span>
															</td>
														</tr>
														<?php } 
														?>
														
													</tbody>
													<!--end::Table body-->
												</table>
												<!--end::Table-->
											</div>
											<!--end::Table container-->
										</div>
									</div>
								</div>
								</div>
								
								
								
							</div>
							<!--end::Row-->
							<!--begin::Row-->
							
							<!--end::Row-->
						</div>
						<!--end::Post-->
					</div>
					<!--end::Container-->
					<!--begin::Footer-->
					<?php include("footer.php"); ?>
					<!--end::Footer-->
				</div>
				<!--end::Wrapper-->
			</div>
			<!--end::Page-->
		</div>

		
		
		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
		</div>
		<script>var hostUrl = "../assets/";</script>
		<script src="../assets/plugins/global/plugins.bundle.js"></script>
		<script src="../assets/js/scripts.bundle.js"></script>
		<script src="../assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="../assets/js/custom/widgets.js"></script>
		<script src="../assets/js/custom/apps/chat/chat.js"></script>
		<script src="../assets/js/custom/modals/create-app.js"></script>
		<script src="../assets/js/custom/modals/upgrade-plan.js"></script>
		<script>
			var mergeSortRight = (itemList, index = 0) => {
			if (index > itemList.length - 1) return;
			const item = itemList[index];
			const date = new Date($(item.children[2]).children().html());
			const nextDate = new Date($(itemList[index +1].children[2]).children().html());
			if (date > nextDate) {
				$(item).insertAfter($(item).next());
				return mergeSortRight(Object.values($("#fluxo_table tbody tr")), index)
			}
			if (index == 0) {
				return mergeSortRight(Object.values($("#fluxo_table tbody tr")), ++index)
			}
			
			const previousDate = new Date($(itemList[index-1].children[2]).children().html());
			console.log('over item ', index);
			console.log('DATE CURRENT IS', date);
			if (date < previousDate) {
				$(item).insertBefore($(item));
				return mergeSortRight(Object.values($("#fluxo_table tbody tr")), --index)
			}
			return mergeSortRight(Object.values($("#fluxo_table tbody tr")), ++index)
			
			}
			$(document).ready(mergeSortRight(Object.values($("#fluxo_table tbody tr"))))
			var elem = document.documentElement;
			function openFullscreen() {
			  if (elem.requestFullscreen) {
				elem.requestFullscreen();
			  } else if (elem.webkitRequestFullscreen) { /* Safari */
				elem.webkitRequestFullscreen();
			  } else if (elem.msRequestFullscreen) { /* IE11 */
				elem.msRequestFullscreen();
			  }
			}

			function closeFullscreen() {
			  if (document.exitFullscreen) {
				document.exitFullscreen();
			  } else if (document.webkitExitFullscreen) { /* Safari */
				document.webkitExitFullscreen();
			  } else if (document.msExitFullscreen) { /* IE11 */
				document.msExitFullscreen();
			  }
			}

			$(document).ready(function() {
			  addToHomescreen();
			});
			if(navigator.userAgent.match(/Android/i)){
				window.scrollTo(0,1);
			}
		</script>
	</body>
</html>