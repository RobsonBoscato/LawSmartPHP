<?php
include("controle_sessao.php");
include("config.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
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
		<link rel="shortcut icon" href="assets/media/misc/LSC-icone.png" />

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, viewport-fit=cover" />
		<meta name="mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black" />
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-title" content="LAW SMART">
		<link rel="shortcut icon" sizes="16x16" href="assets/media/misc/LSC-icone.png">
		<link rel="shortcut icon" sizes="196x196" href="assets/media/misc/LSC-icone.png">
		<link rel="apple-touch-icon-precomposed" href="assets/media/misc/LSC-icone.png">

		<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
		<link href="assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		
		<link rel="apple-touch-icon" sizes="180x180" href="assets/media/misc/LSC-icone.png">
		<link rel="stylesheet" type="text/css" href="addtohomescreen.css">
		<script src="addtohomescreen.js"></script>

	</head>
	<!--end::Head-->
	<!--begin::Body-->
	<body id="kt_body" style="background-image: url('assets/media/misc/page-bg.jpg')" class="page-bg header-fixed header-tablet-and-mobile-fixed aside-enabled">
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
							
							<div class="row g-6 g-xl-9">
								<div class="col-lg-6 col-xxl-6">
									<!--begin::Card-->
									<div class="card h-100">
										<!--begin::Card body-->
										<div class="card-body p-9">
											<?php
												$conta_opera = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaloperacoes FROM operacoes WHERE cnpj = '{$_SESSION["cnpj"]}'")) or die(mysql_error());
											?>
											<div class="fs-2hx fw-bolder"><?php echo $conta_opera['totaloperacoes']; ?></div>
											<div class="fs-4 fw-bold text-gray-400 mb-7">Operações Importadas</div>
											<!--end::Heading-->
											<!--begin::Wrapper-->
											<div class="d-flex flex-wrap">
												<!--begin::Chart-->
												<div class="d-flex flex-center h-100px w-100px me-9 mb-5">
													<canvas id="kt_project_list_chart"></canvas>
												</div>
												<!--end::Chart-->
												<!--begin::Labels-->
												<div class="d-flex flex-column justify-content-center flex-row-fluid pe-11 mb-5">
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-bold align-items-center mb-3">
														<div class="bullet bg-primary me-3"></div>
														<div class="text-gray-400">Duplicatas Ativas</div>
														<?php
															$duplicatas = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = a.id")) or die(mysql_error());
														?>
														<div class="ms-auto fw-bolder text-gray-700"><?php echo $conta_opera['totaloperacoes']-$duplicatas['totaldupli']; ?></div>
													</div>
													<!--end::Label-->
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-bold align-items-center mb-3">
														<div class="bullet bg-success me-3"></div>
														<div class="text-gray-400">Descontadas</div>
														<div class="ms-auto fw-bolder text-gray-700"><?php echo $duplicatas['totaldupli']; ?></div>
													</div>
													<!--end::Label-->
													<!--begin::Label-->
													<div class="d-flex fs-6 fw-bold align-items-center">
														<div class="bullet bg-gray-300 me-3"></div>
														<div class="text-gray-400">Recusadas</div>
														<div class="ms-auto fw-bolder text-gray-700">0</div>
													</div>
													<!--end::Label-->
												</div>
												<!--end::Labels-->
											</div>
											<!--end::Wrapper-->
										</div>
										<!--end::Card body-->
									</div>
									<!--end::Card-->
								</div>
								<div class="col-lg-6 col-xxl-6">
									<!--begin::Budget-->
									<div class="card h-100">
										<div class="card-body p-9">
											<?php
												$totaloperado = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valorOriginal) as valor FROM antecipadas WHERE fornecedor = '{$_SESSION["id"]}'")) or die(mysql_error());
											?>
											<div class="fs-2hx fw-bolder">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></div>
											<div class="fs-4 fw-bold text-gray-400 mb-7">Valor Operado</div>
											<?php
												$totaloperado2 = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valor) as valor FROM operacoes WHERE cnpj = '{$_SESSION["cnpj"]}' AND status = '0'")) or die(mysql_error());
											?>
											<div class="fs-6 d-flex justify-content-between mb-4">
												<div class="fw-bold">Ativo</div>
												<div class="d-flex fw-bolder">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr007.svg-->
												<span class="svg-icon svg-icon-3 me-1 svg-icon-success">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<path d="M13.4 10L5.3 18.1C4.9 18.5 4.9 19.1 5.3 19.5C5.7 19.9 6.29999 19.9 6.69999 19.5L14.8 11.4L13.4 10Z" fill="black" />
														<path opacity="0.3" d="M19.8 16.3L8.5 5H18.8C19.4 5 19.8 5.4 19.8 6V16.3Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->R$ <?php echo number_format($totaloperado2['valor'], 2, ',', '.'); ?></div>
											</div>
											<div class="separator separator-dashed"></div>
											<div class="fs-6 d-flex justify-content-between my-4">
												<div class="fw-bold">Custos de Taxas</div>
												<?php
													$totaloperado3 = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(descontoTaxas) as valor FROM antecipadas WHERE fornecedor = '{$_SESSION["id"]}'")) or die(mysql_error());
												?>
												<div class="d-flex fw-bolder">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr006.svg-->
												<span class="svg-icon svg-icon-3 me-1 svg-icon-danger">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<path d="M13.4 14.8L5.3 6.69999C4.9 6.29999 4.9 5.7 5.3 5.3C5.7 4.9 6.29999 4.9 6.69999 5.3L14.8 13.4L13.4 14.8Z" fill="black" />
														<path opacity="0.3" d="M19.8 8.5L8.5 19.8H18.8C19.4 19.8 19.8 19.4 19.8 18.8V8.5Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->R$ <?php echo number_format($totaloperado3['valor'], 2, ',', '.'); ?></div>
											</div>
											<div class="separator separator-dashed"></div>
											<div class="fs-6 d-flex justify-content-between mt-4">
												<div class="fw-bold">Custos de Juros</div>
												<?php
													$totaloperado4 = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(descontoJuros) as valor FROM antecipadas WHERE fornecedor = '{$_SESSION["id"]}'")) or die(mysql_error());
												?>
												<div class="d-flex fw-bolder">
												<!--begin::Svg Icon | path: icons/duotune/arrows/arr007.svg-->
												<span class="svg-icon svg-icon-3 me-1 svg-icon-success">
													<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
														<path d="M13.4 10L5.3 18.1C4.9 18.5 4.9 19.1 5.3 19.5C5.7 19.9 6.29999 19.9 6.69999 19.5L14.8 11.4L13.4 10Z" fill="black" />
														<path opacity="0.3" d="M19.8 16.3L8.5 5H18.8C19.4 5 19.8 5.4 19.8 6V16.3Z" fill="black" />
													</svg>
												</span>
												<!--end::Svg Icon-->R$ <?php echo number_format($totaloperado4['valor'], 2, ',', '.'); ?></div>
											</div>
										</div>
									</div>
									<!--end::Budget-->
								</div>
								
							</div>
							<!--end::Stats-->
							<!--begin::Toolbar-->
							<div class="d-flex flex-wrap flex-stack my-5">
								<!--begin::Heading-->
								<h2 class="fs-2 fw-bold my-2">Operações
								<span class="fs-6 text-gray-400 ms-1">Ordenados por Status</span></h2>
								<!--end::Heading-->
								<!--begin::Controls-->
								<div class="d-flex flex-wrap my-1">
									<!--begin::Select wrapper-->
									<div class="m-0">
										<!--begin::Select-->
										<!-- <select name="status" data-control="select2" data-hide-search="true" class="form-select form-select-sm form-select-transparent fw-bolder w-125px">
											<option value="Active" selected="selected">Ativas</option>
											<option value="Approved">Completadas</option>
											<option value="Declined">Canceladas</option>
										</select> -->
										<!--end::Select-->
									</div>
									<!--end::Select wrapper-->
								</div>
								<!--end::Controls-->
							</div>
							<!--end::Toolbar-->
							<!--begin::Row-->
							<div class="row g-6 g-xl-9">
								
								<?php
									$antecipadas = mysqli_query($lawsmt, "SELECT * FROM antecipadas WHERE fornecedor = '{$_SESSION['id']}'");
									while($row = mysqli_fetch_assoc($antecipadas)) {
										$quantidade = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = '{$row['id']}'")) or die(mysqli_error());
										$status = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT operacoes.status as stat FROM antecipadas as a, antecipadasDetalhes as ad left join operacoes  on operacoes.id = ad.operacao WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = '{$row['id']}'")) or die(mysqli_error());
								?>
								<div class="col-md-6 col-xl-4">
									<div class="card border-hover-primary">
										
										<div class="card-body p-9">
											<div class="fs-3 fw-bolder text-dark"><?php echo $quantidade['totaldupli']; ?> duplicatas</div>
											<p class="text-gray-400 fw-bold fs-5 mt-1 mb-7">LS-00<?php echo $row['id']; ?></p>
											<div class="d-flex flex-wrap mb-5">
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
													<div class="fs-6 text-gray-800 fw-bolder"><?php $data = new DateTime($row['data']); echo $data->format('d-m-Y'); ?></div>
													<div class="fw-bold text-gray-400">Aprovada</div>
												</div>
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
													<div class="fs-6 text-gray-800 fw-bolder">R$ <?php echo number_format($row['valorOriginal'], 2, ',', '.'); ?></div>
													<div class="fw-bold text-gray-400">Valor Operado</div>
												</div>
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 me-7 mb-3">
													<div class="fs-6 text-gray-800 fw-bolder">R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></div>
													<div class="fw-bold text-gray-400">Valor Final</div>
												</div>
												<?php 
													if ($status["stat"] == 5) {
												?>
													<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
														<div class="fs-6 text-gray-800 fw-bolder">Assinado</div>
													</div>
												<?php		
													} else {
												?>
												<div class="border border-gray-300 border-dashed rounded min-w-125px py-3 px-4 mb-3">
													<div class="fs-6 text-gray-800 fw-bolder">Não assinado</div>
													<!-- <div class="fw-bold"><a href="contrato.php" target="_blank" class="btn btn-success text-white">Assinar</a></div> -->
												</div>
												<?php
													}
												?>
											</div>
											<?php
												if($row["confirmada"] == '1') {
											?>
											<div class="h-10px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="Essa operação já confirmada e paga.">
												<div class="bg-success rounded h-10px" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
											<?php } else { ?>
											<div class="h-10px w-100 bg-light mb-5" data-bs-toggle="tooltip" title="Essa operação não foi confirmada.">
												<div class="bg-danger rounded h-10px" role="progressbar" style="width: 100%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
											<?php } ?>
										</div>
									</div>
								</div>
								<?php } ?>
							</div>
							
							
						</div>
						<!--end::Post-->
					</div>
					<?php include("footer.php"); ?>
				</div>
			</div>
		</div>

		<div id="kt_scrolltop" class="scrolltop" data-kt-scrolltop="true">
			<span class="svg-icon">
				<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
					<rect opacity="0.5" x="13" y="6" width="13" height="2" rx="1" transform="rotate(90 13 6)" fill="black" />
					<path d="M12.5657 8.56569L16.75 12.75C17.1642 13.1642 17.8358 13.1642 18.25 12.75C18.6642 12.3358 18.6642 11.6642 18.25 11.25L12.7071 5.70711C12.3166 5.31658 11.6834 5.31658 11.2929 5.70711L5.75 11.25C5.33579 11.6642 5.33579 12.3358 5.75 12.75C6.16421 13.1642 6.83579 13.1642 7.25 12.75L11.4343 8.56569C11.7467 8.25327 12.2533 8.25327 12.5657 8.56569Z" fill="black" />
				</svg>
			</span>
		</div>
		<script>var hostUrl = "assets/";</script>
		<script src="assets/plugins/global/plugins.bundle.js"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<script src="assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="assets/js/custom/widgets.js"></script>
		<script src="assets/js/custom/apps/chat/chat.js"></script>
		<script src="assets/js/custom/modals/create-app.js"></script>
		<script src="assets/js/custom/modals/upgrade-plan.js"></script>
		<script>
			function abre_dados(id){
	
	
				$('#conteudo_dados').hide();
				$('#modal_dados').modal('show');

				$.ajax({
					type:'POST',
					data:'id='+id,
					url:"dados.php",
					success: function(msg){

						$('#conteudo_dados').html(msg);
						$('#conteudo_dados').fadeIn();


					}
				});

			}
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