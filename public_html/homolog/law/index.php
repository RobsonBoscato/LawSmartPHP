<?php
include("../controle_sessao.php");
include("../config.php");
?>
<!DOCTYPE html>
<html lang="en">
	<!--begin::Head-->
	<head><base href="">
		<title>DASHBOARD ADM</title>
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
	<body id="kt_body" style="background-image: url('../assets/media/misc/page-bg-light.jpg')" class="light-mode page-bg header-fixed header-tablet-and-mobile-fixed aside-enabled">
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
							<div class="row gy-5 g-xl-8 d-flex align-items-center mt-lg-0 mb-10 mb-lg-15">
								<!--begin::Col-->
								<div class="col-xl-8 d-flex align-items-center">
									<h1 class="fs-2hx text-dark">DASHBOARD DE ADMINSITRADOR 
									<br />LAW SMART.</h1>
								</div>
								<!--end::Col-->
								<!--begin::Col-->
								<div class="col-xl-4">
									<div class="d-flex flex-stack ps-lg-20">
										
										<a href="index.php" class="btn btn-icon btn-outline btn-nav active h-50px w-50px h-lg-70px w-lg-70px ms-2">
											<!--begin::Svg Icon | path: icons/duotune/technology/teh008.svg-->
											<span class="svg-icon svg-icon-1 svg-icon-lg-2hx">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path opacity="0.3" d="M11 6.5C11 9 9 11 6.5 11C4 11 2 9 2 6.5C2 4 4 2 6.5 2C9 2 11 4 11 6.5ZM17.5 2C15 2 13 4 13 6.5C13 9 15 11 17.5 11C20 11 22 9 22 6.5C22 4 20 2 17.5 2ZM6.5 13C4 13 2 15 2 17.5C2 20 4 22 6.5 22C9 22 11 20 11 17.5C11 15 9 13 6.5 13ZM17.5 13C15 13 13 15 13 17.5C13 20 15 22 17.5 22C20 22 22 20 22 17.5C22 15 20 13 17.5 13Z" fill="black" />
													<path d="M17.5 16C17.5 16 17.4 16 17.5 16L16.7 15.3C16.1 14.7 15.7 13.9 15.6 13.1C15.5 12.4 15.5 11.6 15.6 10.8C15.7 9.99999 16.1 9.19998 16.7 8.59998L17.4 7.90002H17.5C18.3 7.90002 19 7.20002 19 6.40002C19 5.60002 18.3 4.90002 17.5 4.90002C16.7 4.90002 16 5.60002 16 6.40002V6.5L15.3 7.20001C14.7 7.80001 13.9 8.19999 13.1 8.29999C12.4 8.39999 11.6 8.39999 10.8 8.29999C9.99999 8.19999 9.20001 7.80001 8.60001 7.20001L7.89999 6.5V6.40002C7.89999 5.60002 7.19999 4.90002 6.39999 4.90002C5.59999 4.90002 4.89999 5.60002 4.89999 6.40002C4.89999 7.20002 5.59999 7.90002 6.39999 7.90002H6.5L7.20001 8.59998C7.80001 9.19998 8.19999 9.99999 8.29999 10.8C8.39999 11.5 8.39999 12.3 8.29999 13.1C8.19999 13.9 7.80001 14.7 7.20001 15.3L6.5 16H6.39999C5.59999 16 4.89999 16.7 4.89999 17.5C4.89999 18.3 5.59999 19 6.39999 19C7.19999 19 7.89999 18.3 7.89999 17.5V17.4L8.60001 16.7C9.20001 16.1 9.99999 15.7 10.8 15.6C11.5 15.5 12.3 15.5 13.1 15.6C13.9 15.7 14.7 16.1 15.3 16.7L16 17.4V17.5C16 18.3 16.7 19 17.5 19C18.3 19 19 18.3 19 17.5C19 16.7 18.3 16 17.5 16Z" fill="black" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</a>
										<a href="ancoras.php" class="btn btn-icon btn-outline btn-nav h-50px w-50px h-lg-70px w-lg-70px ms-2">
											<!--begin::Svg Icon | path: icons/duotune/art/art002.svg-->
											<span class="svg-icon svg-icon-1 svg-icon-lg-2hx">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
													<path opacity="0.3" d="M8.9 21L7.19999 22.6999C6.79999 23.0999 6.2 23.0999 5.8 22.6999L4.1 21H8.9ZM4 16.0999L2.3 17.8C1.9 18.2 1.9 18.7999 2.3 19.1999L4 20.9V16.0999ZM19.3 9.1999L15.8 5.6999C15.4 5.2999 14.8 5.2999 14.4 5.6999L9 11.0999V21L19.3 10.6999C19.7 10.2999 19.7 9.5999 19.3 9.1999Z" fill="black" />
													<path d="M21 15V20C21 20.6 20.6 21 20 21H11.8L18.8 14H20C20.6 14 21 14.4 21 15ZM10 21V4C10 3.4 9.6 3 9 3H4C3.4 3 3 3.4 3 4V21C3 21.6 3.4 22 4 22H9C9.6 22 10 21.6 10 21ZM7.5 18.5C7.5 19.1 7.1 19.5 6.5 19.5C5.9 19.5 5.5 19.1 5.5 18.5C5.5 17.9 5.9 17.5 6.5 17.5C7.1 17.5 7.5 17.9 7.5 18.5Z" fill="black" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</a>
										<a href="operacoes.php" class="btn btn-icon btn-outline btn-nav h-50px w-50px h-lg-70px w-lg-70px ms-2">
											<!--begin::Svg Icon | path: icons/duotune/abstract/abs042.svg-->
											<span class="svg-icon svg-icon-1 svg-icon-lg-2hx">
												<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
													<path d="M18 21.6C16.6 20.4 9.1 20.3 6.3 21.2C5.7 21.4 5.1 21.2 4.7 20.8L2 18C4.2 15.8 10.8 15.1 15.8 15.8C16.2 18.3 17 20.5 18 21.6ZM18.8 2.8C18.4 2.4 17.8 2.20001 17.2 2.40001C14.4 3.30001 6.9 3.2 5.5 2C6.8 3.3 7.4 5.5 7.7 7.7C9 7.9 10.3 8 11.7 8C15.8 8 19.8 7.2 21.5 5.5L18.8 2.8Z" fill="black" />
													<path opacity="0.3" d="M21.2 17.3C21.4 17.9 21.2 18.5 20.8 18.9L18 21.6C15.8 19.4 15.1 12.8 15.8 7.8C18.3 7.4 20.4 6.70001 21.5 5.60001C20.4 7.00001 20.2 14.5 21.2 17.3ZM8 11.7C8 9 7.7 4.2 5.5 2L2.8 4.8C2.4 5.2 2.2 5.80001 2.4 6.40001C2.7 7.40001 3.00001 9.2 3.10001 11.7C3.10001 15.5 2.40001 17.6 2.10001 18C3.20001 16.9 5.3 16.2 7.8 15.8C8 14.2 8 12.7 8 11.7Z" fill="black" />
												</svg>
											</span>
											<!--end::Svg Icon-->
										</a>
										
										
									</div>
								</div>
								<!--end::Col-->
							</div>
							
							
							
							<div class="row g-5 g-xl-8">
								<div class="col-xl-6">
									<!--begin::Charts Widget 4-->
									<div class="card card-xl-stretch mb-5 mb-xl-8">
										<!--begin::Header-->
										<div class="card-header border-0 pt-5">
											<h3 class="card-title align-items-start flex-column">
												<span class="card-label fw-bolder fs-3 mb-1">Evolução Agrupada</span>
												<span class="text-muted fw-bold fs-7">Crescimento dos Ancoras comparados com o CDI</span>
											</h3>
											
										</div>
										<!--end::Header-->
										<!--begin::Body-->
										<div class="card-body">
											<!--begin::Chart-->
											<div id="kt_charts_widget_4_chart" style="height: 350px"></div>
											<!--end::Chart-->
										</div>
										<!--end::Body-->
									</div>
									<!--end::Charts Widget 4-->
								</div>
								<div class="col-xl-6">
									<div class="row g-5 g-xl-8">
										<div class="col-md-12">
											<div class="card card-xl-stretch mb-xl-8">
												<!--begin::Header-->
												<div class="card-header border-0 pt-5">
													<h3 class="card-title align-items-start flex-column">
														<span class="card-label fw-bolder fs-3 mb-1">Liquidez</span>
														<span class="text-muted fw-bold fs-7">Avaliando operações aprovadas</span>
													</h3>
													<!--begin::Toolbar-->
												
												</div>
												<!--end::Header-->
												<!--begin::Body-->
												<div class="card-body">
													<!--begin::Chart-->
													<div id="kt_charts_widget_3_chart" style="height: 150px!important; min-height: 150px!important;"></div>
													<!--end::Chart-->
												</div>
												<!--end::Body-->
											</div>
										</div>
									</div>
									
									<div class="row g-5 g-xl-8">
										<div class="col-xl-6">
											<!--begin::Statistics Widget 5-->
											<a href="#" class="card bg-body hoverable card-xl-stretch mb-xl-8">
												<!--begin::Body-->
												<div class="card-body">
													<!--begin::Svg Icon | path: icons/duotune/general/gen032.svg-->
													<span class="svg-icon svg-icon-primary svg-icon-3x ms-n1">
														<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
															<rect x="8" y="9" width="3" height="10" rx="1.5" fill="black"></rect>
															<rect opacity="0.5" x="13" y="5" width="3" height="14" rx="1.5" fill="black"></rect>
															<rect x="18" y="11" width="3" height="8" rx="1.5" fill="black"></rect>
															<rect x="3" y="13" width="3" height="6" rx="1.5" fill="black"></rect>
														</svg>
													</span>
													<!--end::Svg Icon-->
													<div class="text-gray-900 fw-bolder fs-2 mb-2 mt-5">R$ 5.000.000,00</div>
													<div class="fw-bold text-gray-400"><small>Valor Aplicado (Prazo médio <strong>90 dias</strong>)</small></div>
												</div>
												<!--end::Body-->
											</a>
											<!--end::Statistics Widget 5-->
										</div>
										
										
										<div class="col-xl-6">
											<!--begin::Statistics Widget 5-->
											<a href="#" class="card bg-info hoverable card-xl-stretch mb-5 mb-xl-8">
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
														$totaloperado = mysql_fetch_assoc(mysql_query("SELECT SUM(descontoJuros) as juros FROM antecipadas")) or die(mysql_error());
														$totaltaxas = mysql_fetch_assoc(mysql_query("SELECT SUM(descontoTaxas) as taxas FROM antecipadas")) or die(mysql_error());
													?>
													<div class="text-white fw-bolder fs-2 mb-2 mt-5">R$ <?php echo number_format("5000000" + $totaloperado['juros'] + $totaltaxas['taxas'], 2, ',', '.'); ?></div>
													<div class="fw-bold text-white"><small>Valor Atualizado {Taxa de Juros <strong>2.4% a.m.</strong>)</small></div>
												</div>
												<!--end::Body-->
											</a>
											<!--end::Statistics Widget 5-->
										</div>
									</div>
								</div>
								
							</div>
							
							<div class="row g-5 g-xl-8">
								<div class="col-xl-4">
									<!--begin::Statistics Widget 4-->
									<div class="card card-xl-stretch mb-xl-8">
										<!--begin::Body-->
										<div class="card-body p-0">
											<div class="d-flex flex-stack card-p flex-grow-1">
												<span class="symbol symbol-50px me-2">
													<span class="symbol-label bg-light-info">
														<!--begin::Svg Icon | path: icons/duotune/ecommerce/ecm002.svg-->
														<span class="svg-icon svg-icon-2x svg-icon-info">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path d="M21 10H13V11C13 11.6 12.6 12 12 12C11.4 12 11 11.6 11 11V10H3C2.4 10 2 10.4 2 11V13H22V11C22 10.4 21.6 10 21 10Z" fill="black" />
																<path opacity="0.3" d="M12 12C11.4 12 11 11.6 11 11V3C11 2.4 11.4 2 12 2C12.6 2 13 2.4 13 3V11C13 11.6 12.6 12 12 12Z" fill="black" />
																<path opacity="0.3" d="M18.1 21H5.9C5.4 21 4.9 20.6 4.8 20.1L3 13H21L19.2 20.1C19.1 20.6 18.6 21 18.1 21ZM13 18V15C13 14.4 12.6 14 12 14C11.4 14 11 14.4 11 15V18C11 18.6 11.4 19 12 19C12.6 19 13 18.6 13 18ZM17 18V15C17 14.4 16.6 14 16 14C15.4 14 15 14.4 15 15V18C15 18.6 15.4 19 16 19C16.6 19 17 18.6 17 18ZM9 18V15C9 14.4 8.6 14 8 14C7.4 14 7 14.4 7 15V18C7 18.6 7.4 19 8 19C8.6 19 9 18.6 9 18Z" fill="black" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
												</span>
												<div class="d-flex flex-column text-end">
													<span class="text-muted fw-bold mt-1">Disponível</span>
													<?php
														$totaloperado = mysql_fetch_assoc(mysql_query("SELECT SUM(valor) as valor FROM antecipadas")) or die(mysql_error());
														$conta_opera = mysql_fetch_assoc(mysql_query("SELECT count(*) as totaloperacoes FROM operacoes")) or die(mysql_error());
													?>
													<span class="text-dark fw-bolder fs-2">R$ <?php echo number_format("5000000" - $totaloperado['valor'], 2, ',', '.'); ?></span>
													<span class="text-muted fw-bold mt-1">Em <?php echo $conta_opera['totaloperacoes']; ?> títulos</span>
												</div>
											</div>
										</div>
										<!--end::Body-->
									</div>
									<!--end::Statistics Widget 4-->
								</div>
								<div class="col-xl-4">
									<!--begin::Statistics Widget 4-->
									<div class="card card-xl-stretch mb-xl-8">
										<!--begin::Body-->
										<div class="card-body p-0">
											<div class="d-flex flex-stack card-p flex-grow-1">
												<span class="symbol symbol-50px me-2">
													<span class="symbol-label bg-light-success">
														<!--begin::Svg Icon | path: icons/duotune/finance/fin001.svg-->
														<span class="svg-icon svg-icon-2x svg-icon-success">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path d="M20 19.725V18.725C20 18.125 19.6 17.725 19 17.725H5C4.4 17.725 4 18.125 4 18.725V19.725H3C2.4 19.725 2 20.125 2 20.725V21.725H22V20.725C22 20.125 21.6 19.725 21 19.725H20Z" fill="black" />
																<path opacity="0.3" d="M22 6.725V7.725C22 8.325 21.6 8.725 21 8.725H18C18.6 8.725 19 9.125 19 9.725C19 10.325 18.6 10.725 18 10.725V15.725C18.6 15.725 19 16.125 19 16.725V17.725H15V16.725C15 16.125 15.4 15.725 16 15.725V10.725C15.4 10.725 15 10.325 15 9.725C15 9.125 15.4 8.725 16 8.725H13C13.6 8.725 14 9.125 14 9.725C14 10.325 13.6 10.725 13 10.725V15.725C13.6 15.725 14 16.125 14 16.725V17.725H10V16.725C10 16.125 10.4 15.725 11 15.725V10.725C10.4 10.725 10 10.325 10 9.725C10 9.125 10.4 8.725 11 8.725H8C8.6 8.725 9 9.125 9 9.725C9 10.325 8.6 10.725 8 10.725V15.725C8.6 15.725 9 16.125 9 16.725V17.725H5V16.725C5 16.125 5.4 15.725 6 15.725V10.725C5.4 10.725 5 10.325 5 9.725C5 9.125 5.4 8.725 6 8.725H3C2.4 8.725 2 8.325 2 7.725V6.725L11 2.225C11.6 1.925 12.4 1.925 13.1 2.225L22 6.725ZM12 3.725C11.2 3.725 10.5 4.425 10.5 5.225C10.5 6.025 11.2 6.725 12 6.725C12.8 6.725 13.5 6.025 13.5 5.225C13.5 4.425 12.8 3.725 12 3.725Z" fill="black" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
												</span>
												<div class="d-flex flex-column text-end">
													<span class="text-muted fw-bold mt-1">Ofertado</span>
													<?php
														$totaloperado = mysql_fetch_assoc(mysql_query("SELECT SUM(valor) as valor FROM antecipadas")) or die(mysql_error());
														$conta_opera = mysql_fetch_assoc(mysql_query("SELECT count(*) as antecipadas FROM antecipadasDetalhes")) or die(mysql_error());
													?>
													<span class="text-dark fw-bolder fs-2">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></span>
													<span class="text-muted fw-bold mt-1">Em <?php echo $conta_opera['antecipadas']; ?>  títulos</span>
												</div>
											</div>
										</div>
										<!--end::Body-->
									</div>
									<!--end::Statistics Widget 4-->
								</div>
								<div class="col-xl-4">
									<!--begin::Statistics Widget 4-->
									<div class="card card-xl-stretch mb-5 mb-xl-8">
										<!--begin::Body-->
										<div class="card-body p-0">
											<div class="d-flex flex-stack card-p flex-grow-1">
												<span class="symbol symbol-50px me-2">
													<span class="symbol-label bg-light-primary">
														<!--begin::Svg Icon | path: icons/duotune/finance/fin006.svg-->
														<span class="svg-icon svg-icon-2x svg-icon-primary">
															<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																<path opacity="0.3" d="M20 15H4C2.9 15 2 14.1 2 13V7C2 6.4 2.4 6 3 6H21C21.6 6 22 6.4 22 7V13C22 14.1 21.1 15 20 15ZM13 12H11C10.5 12 10 12.4 10 13V16C10 16.5 10.4 17 11 17H13C13.6 17 14 16.6 14 16V13C14 12.4 13.6 12 13 12Z" fill="black" />
																<path d="M14 6V5H10V6H8V5C8 3.9 8.9 3 10 3H14C15.1 3 16 3.9 16 5V6H14ZM20 15H14V16C14 16.6 13.5 17 13 17H11C10.5 17 10 16.6 10 16V15H4C3.6 15 3.3 14.9 3 14.7V18C3 19.1 3.9 20 5 20H19C20.1 20 21 19.1 21 18V14.7C20.7 14.9 20.4 15 20 15Z" fill="black" />
															</svg>
														</span>
														<!--end::Svg Icon-->
													</span>
												</span>
												<div class="d-flex flex-column text-end">
													<span class="text-muted fw-bold mt-1">Efetivado no mês</span>
													<span class="text-dark fw-bolder fs-2">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></span>
													<span class="text-muted fw-bold mt-1">Em <?php echo $conta_opera['antecipadas']; ?> títulos</span>
												</div>
											</div>
										</div>
										<!--end::Body-->
									</div>
									<!--end::Statistics Widget 4-->
								</div>
							</div>
							
							<div class="row gy-5 g-xl-8">
								<!--begin::Col-->
								<div class="col-xl-12">
									<div class="col-xl-12">
									<div class="card mb-5 mb-xl-8">
										<div class="card-header border-0 pt-5">
											<h3 class="card-title align-items-start flex-column">
												<span class="card-label fw-bolder fs-3 mb-1">Últimas Operações Realizadas</span>
												<span class="text-muted mt-1 fw-bold fs-7">Abaixo listamos as últimas operações realizadas.</span>
											</h3>
											
										</div>
										<div class="card-body py-3">
											<!--begin::Table container-->
											<div class="table-responsive">
												<!--begin::Table-->
												<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3">
													<!--begin::Table head-->
													<thead>
														<tr class="fw-bolder text-muted">
															<th class="min-w-200px">Fornecedor</th>
															<th class="min-w-100px">Transação Id</th>
															<th class="min-w-120px">Data</th>
															<th class="min-w-80px">Valor</th>
															<th class="min-w-120px">Status</th>
														</tr>
													</thead>
													<tbody>
														
														<?php
															$antecipadas = mysql_query("SELECT * FROM antecipadas");
															while($row = mysql_fetch_assoc($antecipadas)) {
																$quantidade = mysql_fetch_assoc(mysql_query("SELECT count(*) as totaldupli FROM antecipadas as a, antecipadasDetalhes as ad WHERE a.fornecedor = '{$_SESSION["id"]}' AND ad.antecipada = '{$row['id']}'")) or die(mysql_error());
																$fornecedor = mysql_fetch_assoc(mysql_query("SELECT * FROM fornecedores WHERE id = '{$row['fornecedor']}'")) or die(mysql_error());
														?>
														<tr>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6"><?php echo $fornecedor["razao"]; ?></a>
																<span class="text-muted fw-bold text-muted d-block fs-7"><?php echo $fornecedor["cnpj"]; ?></span>
															</td>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary fs-6">0000<?php echo $row["id"]; ?></a>
															</td>
															
															<td>
																<div class="fs-6 text-gray-800 fw-bolder"><?php $data = new DateTime($row['data']); echo $data->format('d-M-Y'); ?></div>
																<span class="text-muted fw-bold text-muted d-block fs-7">link para nota</span>
															</td>
															<td>
																<div class="fs-6 text-gray-800 fw-bolder">R$ <?php echo number_format($row['valor'], 2, ',', '.'); ?></div>
															</td>
															<td>
																<span class="badge badge-light-success">Liberada</span>
															</td>
															
														</tr>
														<?php } ?>
														
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