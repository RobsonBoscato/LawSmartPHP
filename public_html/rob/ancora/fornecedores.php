<?php
include("../controle_sessao.php");
include("../config.php");


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
		<link href="../assets/plugins/custom/datatables/datatables.bundle.css" rel="stylesheet" type="text/css"/>
		<link href="../assets/plugins/custom/fullcalendar/fullcalendar.bundle.css" rel="stylesheet" type="text/css" />
		<link href="../assets/plugins/global/plugins.bundle.css" rel="stylesheet" type="text/css" />
		<link href="../assets/css/style.bundle.css" rel="stylesheet" type="text/css" />
		
		<link rel="apple-touch-icon" sizes="180x180" href="../assets/media/misc/LSC-icone.png">
		<link rel="stylesheet" type="text/css" href="../addtohomescreen.css">
		<script src="../addtohomescreen.js"></script>

	</head>
	
	<?php
	$acao = mysqli_escape_string($lawsmt, $_GET['a']);
	if($acao == 'alterarDados') {
		$id = mysqli_escape_string($lawsmt, $_GET['id']);

		$representante = mysqli_escape_string($lawsmt, ($_POST['representante']));
		$cpf = mysqli_escape_string($lawsmt, ($_POST['cpf']));
		$telefone = mysqli_escape_string($lawsmt, $_POST['telefone']);
		$email = mysqli_escape_string($lawsmt, $_POST['email']);
		$rua = mysqli_escape_string($lawsmt, ($_POST['rua']));
		$numero = mysqli_escape_string($lawsmt, $_POST['numero']);
		$bairro = mysqli_escape_string($lawsmt, ($_POST['bairro']));
		$cidade = mysqli_escape_string($lawsmt, ($_POST['cidade']));
		$estado = mysqli_escape_string($lawsmt, $_POST['estado']);
		$limite = mysqli_escape_string($lawsmt, $_POST['limite']);
		$juros = mysqli_escape_string($lawsmt, $_POST['juros']);
		$boleto = mysqli_escape_string($lawsmt, $_POST['boleto']);
		$tac = mysqli_escape_string($lawsmt, $_POST['tac']);
		$ted = mysqli_escape_string($lawsmt, $_POST['ted']);

		$query = mysqli_query($lawsmt, "UPDATE fornecedores SET representante = '{$representante}', cpf = '{$cpf}', telefone = '{$telefone}', email = '{$email}', rua = '{$rua}', numero = '{$numero}', bairro = '{$bairro}', cidade = '{$cidade}', estado = '{$estado}', limite = '{$limite}', juros = '{$juros}', boleto = '{$boleto}', tac = '{$tac}', ted = '{$ted}', updated = 1 WHERE id = '{$id}'") or die(mysqli_error());
		echo "<script>alert('Dados alterados com sucesso.');</script>";
	}
	?>
	
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
													<?php
														$montantepositivo = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valor) as aportes FROM movimentacao WHERE tipo = 'aporte'")) or die(mysqli_error());
														$montantenegativo = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valor) as retiradas FROM movimentacao WHERE tipo = 'retirada'")) or die(mysqli_error());
														$totaloperado = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valor) as valor FROM antecipadas")) or die(mysql_error());
														$valormontante=($montantepositivo["aportes"] - $montantenegativo["retiradas"] - $totaloperado["valor"]);
													?>
													<div class="text-gray-900 fw-bolder fs-2 mb-2 mt-5">R$ <?php echo number_format($valormontante, 2, ',', '.'); ?></div>
													<div class="fw-bold text-gray-400">EM caixa</div>
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
														$totaloperado = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valor) as valor FROM antecipadas")) or die(mysql_error());
														$conta_opera = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT count(*) as totaloperacoes FROM operacoes")) or die(mysql_error());
													?>
													<div class="text-white fw-bolder fs-2 mb-2 mt-5">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></div>
													<div class="fw-bold text-white">Em Operação</div>
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
									<div class="card mb-5 mb-xl-8">
										<div class="card-header border-0 pt-5">
											<h3 class="card-title align-items-start flex-column">
												<span class="card-label fw-bolder fs-3 mb-1">Clientes Habilitados</span>
												<span class="text-muted mt-1 fw-bold fs-7">Abaixo listamos as todos fornecedores disponiveis no sistema.</span>
											</h3>
											
										</div>
										<div class="card-body py-3">
											
											<div class="card card-p-0 card-flush">
											 <div class="card-header align-items-center py-5 gap-2 gap-md-5">
											  <div class="card-title">
											   <!--begin::Search-->
											   <div class="d-flex align-items-center position-relative my-1">
												<span class="svg-icon svg-icon-1 position-absolute ms-4">...</span>
												<input type="text" data-kt-filter="search" class="form-control form-control-solid w-250px ps-14" placeholder="Filtre na tabela" />
											   </div>
											   <!--end::Search-->
											   <!--begin::Export buttons-->
											   <div id="kt_datatable_example_1_export" class="d-none"></div>
											   <!--end::Export buttons-->
											  </div>
											  <div class="card-toolbar flex-row-fluid justify-content-end gap-5">
											   <!--begin::Export dropdown-->
											   <button type="button" class="btn btn-light-primary" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
											   Exportar
											   </button>
											   <!--begin::Menu-->
											   <div id="kt_datatable_example_1_export_menu" class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-200px py-4" data-kt-menu="true">
												<!--begin::Menu item-->
												<div class="menu-item px-3">
												 <a href="#" class="menu-link px-3" data-kt-export="copy">
												 Copiar
												 </a>
												</div>
												<!--end::Menu item-->
												<!--begin::Menu item-->
												<div class="menu-item px-3">
												 <a href="#" class="menu-link px-3" data-kt-export="excel">
												 Excel
												 </a>
												</div>
												<!--end::Menu item-->
												<!--begin::Menu item-->
												<div class="menu-item px-3">
												 <a href="#" class="menu-link px-3" data-kt-export="csv">
												 CSV
												 </a>
												</div>
												<!--end::Menu item-->
												<!--begin::Menu item-->
												<div class="menu-item px-3">
												 <a href="#" class="menu-link px-3" data-kt-export="pdf">
												 PDF
												 </a>
												</div>
												<!--end::Menu item-->
											   </div>
											   <!--end::Menu-->
											   <!--end::Export dropdown-->
											  </div>
											 </div>
											</div>
											<div class="table-responsive">
												<!--begin::Table-->
												<table class="table table-row-bordered table-row-gray-100 align-middle gs-0 gy-3" id="kt_datatable_example_1">
													<!--begin::Table head-->
													<thead>
														<tr class="fw-bolder text-muted">
															<th class="min-w-80px">Tiop</th>
															<th class="min-w-200px">Cliente</th>
															<th class="min-w-100px">CNPJ</th>
															<th class="min-w-120px">Limite</th>
															<th class="min-w-60px">Juros</th>
															<th class="min-w-80px">Valor Operado</th>
															<th class="min-w-120px">Status</th>
															<th class="min-w-100px text-end"></th>
														</tr>
													</thead>
													<tbody>
														<?php
															$fornecedores = mysqli_query($lawsmt, "SELECT * FROM fornecedores");
															while($row = mysqli_fetch_assoc($fornecedores)) {
																$totaloperado = mysqli_fetch_assoc(mysqli_query($lawsmt, "SELECT SUM(valorOriginal) as valor FROM antecipadas WHERE fornecedor = '{$row['id']}'"));
														?>
														<tr>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo $row['tipo']; ?></a>
															</td>
															<td>
																<a href="fornecedor.php?id=<?php echo $row['id']; ?>" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6"><?php echo $row['razao']; ?></a>
																<a href="mailto:<?php echo $row['email']; ?>" class="text-dark text-hover-primary d-block mb-1 fs-8"><?php echo $row['email']; ?></a>
															</td>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary fs-6"><?php echo $row['cnpj']; ?></a>
															</td>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6"><?php echo $row['limite']; ?></a>
															</td>
															<td>
																<a href="#" class="text-dark fw-bolder text-hover-primary d-block mb-1 fs-6"><?php echo $row['juros']; ?></a>
															</td>
															<td class="text-dark fw-bolder text-hover-primary fs-6">R$ <?php echo number_format($totaloperado['valor'], 2, ',', '.'); ?></td>
															<td>
																<span class="badge badge-light-success">Liberada</span>
															</td>
															<td>
																<div class="d-flex justify-content-end flex-shrink-0">
																	<span onClick="abre_dados(<?php echo $row['id'] ?>);" class="btn btn-icon btn-bg-light btn-active-color-primary btn-sm me-1">
																		<!--begin::Svg Icon | path: icons/duotune/general/gen019.svg-->
																		<span class="svg-icon svg-icon-3">
																			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
																				<path d="M17.5 11H6.5C4 11 2 9 2 6.5C2 4 4 2 6.5 2H17.5C20 2 22 4 22 6.5C22 9 20 11 17.5 11ZM15 6.5C15 7.9 16.1 9 17.5 9C18.9 9 20 7.9 20 6.5C20 5.1 18.9 4 17.5 4C16.1 4 15 5.1 15 6.5Z" fill="black"></path>
																				<path opacity="0.3" d="M17.5 22H6.5C4 22 2 20 2 17.5C2 15 4 13 6.5 13H17.5C20 13 22 15 22 17.5C22 20 20 22 17.5 22ZM4 17.5C4 18.9 5.1 20 6.5 20C7.9 20 9 18.9 9 17.5C9 16.1 7.9 15 6.5 15C5.1 15 4 16.1 4 17.5Z" fill="black"></path>
																			</svg>
																		</span>
																		<!--end::Svg Icon-->
																	</span>
																	
																</div>
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
		
		
	
		
		<div class="modal fade" id="modal_dados" tabindex="-1" aria-labelledby="modal_dados" aria-hidden="true">
		  <div class="modal-dialog">
			<div class="modal-content" id="conteudo_dados">
			
			</div>
		  </div>
		</div>
		
		
		<script>var hostUrl = "../assets/";</script>
		<script src="../assets/plugins/global/plugins.bundle.js"></script>
		<script src="../assets/js/scripts.bundle.js"></script>
		<script src="../assets/plugins/custom/fullcalendar/fullcalendar.bundle.js"></script>
		<script src="../assets/js/custom/widgets.js"></script>
		<script src="../assets/js/custom/apps/chat/chat.js"></script>
		<script src="../assets/js/custom/modals/create-app.js"></script>
		<script src="../assets/js/custom/modals/upgrade-plan.js"></script>
		<script src="../assets/plugins/custom/datatables/datatables.bundle.js"></script>
		<script>
			"use strict";

			// Class definition
			var KTDatatablesButtons = function () {
				// Shared variables
				var table;
				var datatable;

				// Private functions
				var initDatatable = function () {
					// Set date data order
					const tableRows = table.querySelectorAll('tbody tr');

					tableRows.forEach(row => {
						const dateRow = row.querySelectorAll('td');
						const realDate = moment(dateRow[2].innerHTML, "DD MMM YYYY, LT").format(); // select date from 4th column in table
						dateRow[3].setAttribute('data-order', realDate);
					});

					// Init datatable --- more info on datatables: https://datatables.net/manual/
					datatable = $(table).DataTable({
						"info": false,
						'order': [],
						'pageLength': 50,
					});
				}

				// Hook export buttons
				var exportButtons = () => {
					const documentTitle = 'Operações disponiveis - LAWSMART';
					var buttons = new $.fn.dataTable.Buttons(table, {
						buttons: [
							{
								extend: 'copyHtml5',
								title: documentTitle
							},
							{
								extend: 'excelHtml5',
								title: documentTitle
							},
							{
								extend: 'csvHtml5',
								title: documentTitle
							},
							{
								extend: 'pdfHtml5',
								title: documentTitle
							}
						]
					}).container().appendTo($('#kt_datatable_example_1_export'));

					// Hook dropdown menu click event to datatable export buttons
					const exportButtons = document.querySelectorAll('#kt_datatable_example_1_export_menu [data-kt-export]');
					exportButtons.forEach(exportButton => {
						exportButton.addEventListener('click', e => {
							e.preventDefault();

							// Get clicked export value
							const exportValue = e.target.getAttribute('data-kt-export');
							const target = document.querySelector('.dt-buttons .buttons-' + exportValue);

							// Trigger click event on hidden datatable export buttons
							target.click();
						});
					});
				}

				// Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
				var handleSearchDatatable = () => {
					const filterSearch = document.querySelector('[data-kt-filter="search"]');
					filterSearch.addEventListener('keyup', function (e) {
						datatable.search(e.target.value).draw();
					});
				}

				// Public methods
				return {
					init: function () {
						table = document.querySelector('#kt_datatable_example_1');

						if ( !table ) {
							return;
						}

						initDatatable();
						exportButtons();
						handleSearchDatatable();
					}
				};
			}();

			// On document ready
			KTUtil.onDOMContentLoaded(function () {
				KTDatatablesButtons.init();
			});
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