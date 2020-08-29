@extends('layouts.app')

@section('htmlheadassets')
	<style>
		.thumbnail {
			box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.5);
			transition: 0.3s;
			min-width: 5%;
			border-radius: 5px;
		}
		.thumbnail-description {
			min-height: 5px;
		}
		.thumbnail:hover {
			cursor: pointer;
			box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 1);
		}
	</style>
@endsection

@section('content')
	<div class="col-12 text-right">
		<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#add-pdf">
			<i class="fas fa-plus-circle"></i>
			Add new document
		</button>
	</div>
	<hr/>

	<div class="row">
		@foreach($files as $row)
			<div class="col-3 justify-content-center align-items-center d-inline-flex p-2">
				<figure
					class="figure text-center thumbnail"
					style="width:210px; height:120px;"
					data-id = "{{ $row->id }}"
					data-modal-title="{{ __('scandinavian.title-modal') }}"
					data-toggle="modal"
					data-target="#reportModal"
					style="cursor: pointer;"
				>
	  				<i class="far fa-file-pdf fa-4x m-2"></i>
					<figcaption class="figure-caption text-center w-80">
						@foreach($row->file_name as $key => $text)
							{{ $text }}<br/>
						@endforeach
					</figcaption>
				</figure>
			</div>
		@endforeach
	</div>

	<div class="container">
		<div class="col-12 justify-content-center align-items-center d-inline-flex small">
			{{ $files->render() }}
		</div>
	</div>

	<!-- Creat -->
	<div
		class="modal fade"
		id="add-pdf"
		tabindex="-1"
		role="dialog"
		aria-labelledby="myModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-info">
					<h5 class="modal-title">{{ __('scandinavian.modal-list-title') }}</h5>
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				</div>
				<form action='{{ URL("scandinavian/world/test") }}' method="post" enctype="multipart/form-data">
					{{ csrf_field() }}
					<div class="modal-body">
						<div class="col-12">
							<div class="row bg-light my-1 tick-border-right">
								<label class="col-4 col-sm-4 col-md-3 col-lg-3 col-xl-4 col-form-label text-md-right">
									{{ __('scandinavian.title-file') }}:
								</label>

								<div class="col-8 col-sm-8 col-md-6 col-lg-6 col-xl-7 my-1">
									<div class="custom-file">
										<input type="file" name="file" class="custom-file-input" required />
										<label class="custom-file-label">
											{{ __('scandinavian.check-file') }}
										</label>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer">
						<button class="btn btn-secondary btn-sm text-white" data-dismiss="modal">
							&times;
							{{ __('scandinavian.btn-cencel') }}
						</button>
						<button class="btn btn-success btn-sm text-white" type="submit">
							<i class="fas fa-plus-circle"></i>
							{{ __('scandinavian.btn-add') }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
  
	<!-- show -->
	<div class="modal fade" id="reportModal" tabindex="-1" role="dialog" >
		<div class="modal-dialog modal-dialog-centered mw-100 w-75" role="document">
			<div class="modal-content">
				<div class="modal-header bg-info">
					<h5 class="modal-title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close" id="modal-close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body modalContent">
					
				</div>
			</div>
		</div>
	</div>
@endsection

@section('htmlbodyassets')
	<script>
		$(".custom-file-input").on("change", function() {
			var fileName = $(this).val().split("\\").pop();
			$(this).siblings(".custom-file-label").addClass("selected").html(fileName);
		});

		$('#reportModal').on('show.bs.modal', function (e) {
			$el = $(this);
			$el.find('.modal-title').text("").text($(e.relatedTarget).data('modal-title'));
			var newHTML = "";
			newHTML += '<h4 class="text-center">Loading...</h4>';
			newHTML += '<br /><div class="progress">';
			newHTML += '<div class="progress-bar progress-bar-striped progress-bar-info progress-bar-animated" role="progressbar"';
			newHTML += 'aria-valuenow="100" aria-valuemin="0" aria-valuemax="200" style="width: 100%">';
			newHTML += '</div></div><br />';
			$el.find(".modal-body").html(newHTML);

			var url = '/scandinavian/world/test/'+$(e.relatedTarget).data('id');
			$.getJSON (
				url,
				function(result) {
					var data = '';
					if (result.url != 'error') {
						data += '<div style="text-align: center;">';
						data += '<iframe src="'+result.url+'" style="width:100%; height:800px;" frameborder="0">';
						data += '</iframe></div>';
					} else {
						data += '<div class="col-12 justify-content-center align-items-center d-inline-flex">';
						data += '<h3>{{ __("scandinavian.msg_not_found") }}</h3>';
						data += '</div>';
					}
					$('.modalContent').html(data);
				}
			);
		});
	</script>
@endsection
