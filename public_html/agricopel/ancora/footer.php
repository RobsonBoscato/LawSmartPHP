<div class="modal" tabindex="-1" id="exampleModal">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-floating p-3" action="operacoesxml.php" method="GET">

				<div class="modal-header">
					<h5 class="modal-title">Exportar Operações para XML</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-5">
							<input type="date" class="form-control-sm is-invalid" id="floatingInputInvalid" name="inicio" placeholder="Data Inicial" value="">

						</div>
						<div class="col-2 mt-2"> até</div>
						<div class="col-5">
							<input type="date" class="form-control-sm is-invalid" id="floatingInputInvalid" name="termino" placeholder="Data Final" value="">

						</div>

					</div>
				</div>
				<div class="modal-footer">
					<button type="submit" id="expoerfile" class="btn btn-sm w-100 btn-primary">Exportar Arquivo</button>
				</div>
			</form>

		</div>
	</div>
</div>
<div class="footer py-4 d-flex flex-lg-column" id="kt_footer">
	<!--begin::Container-->
	<div class="container-xxl d-flex flex-column flex-md-row align-items-center justify-content-between">
		<!--begin::Copyright-->
		<div class="text-dark order-2 order-md-1">
			<span class="text-gray-700 fw-bold me-1"><?php echo date("Y"); ?>©</span>
			<a href="https://lawsecsa.com.br" target="_blank" class="text-gray-800 text-hover-primary">LAW SEC</a>
		</div>
		<!--end::Copyright-->
		<!--begin::Menu-->
		<ul class="menu menu-gray-700 menu-hover-primary fw-bold order-1">
			<li class="menu-item">
				<a href="#" target="_blank" class="menu-link px-2">Sobre</a>
			</li>
			<li class="menu-item">
				<a href="#" target="_blank" class="menu-link px-2">Suporte</a>
			</li>
		</ul>
		<!--end::Menu-->
	</div>
	<!--end::Container-->
</div>