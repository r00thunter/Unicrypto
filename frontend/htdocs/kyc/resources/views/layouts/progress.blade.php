<div class="col-md-12 col-sm-12 col-xs-12">
	@if(auth()->user()->completion == 100)
	<p>We will verify your uploaded documents and activate your account.</p>
	@endif
	<h5>Profile Completion</h5>
    <div class="progress">
        <div class="progress-bar bg-success" role="progressbar" style="width: {{auth()->user()->completion}}%;" aria-valuenow="{{auth()->user()->completion}}" aria-valuemin="0" aria-valuemax="100">{{auth()->user()->completion}}%</div>
    </div>
</div>
