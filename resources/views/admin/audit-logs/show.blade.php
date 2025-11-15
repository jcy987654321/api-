@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Audit Log Details</h1>

    <div class="card">
        <div class="card-body">
            <dl class="row">
                <dt class="col-sm-3">ID</dt>
                <dd class="col-sm-9">{{ $auditLog->id }}</dd>

                <dt class="col-sm-3">User</dt>
                <dd class="col-sm-9">{{ $auditLog->user ? $auditLog->user->name : 'System' }}</dd>

                <dt class="col-sm-3">Module</dt>
                <dd class="col-sm-9"><span class="badge bg-primary">{{ $auditLog->module }}</span></dd>

                <dt class="col-sm-3">Action</dt>
                <dd class="col-sm-9"><span class="badge bg-secondary">{{ $auditLog->action }}</span></dd>

                <dt class="col-sm-3">Description</dt>
                <dd class="col-sm-9">{{ $auditLog->description }}</dd>

                <dt class="col-sm-3">Entity</dt>
                <dd class="col-sm-9">{{ $auditLog->entity_type }} #{{ $auditLog->entity_id }}</dd>

                <dt class="col-sm-3">IP Address</dt>
                <dd class="col-sm-9">{{ $auditLog->ip_address }}</dd>

                <dt class="col-sm-3">User Agent</dt>
                <dd class="col-sm-9"><small>{{ $auditLog->user_agent }}</small></dd>

                <dt class="col-sm-3">Timestamp</dt>
                <dd class="col-sm-9">{{ $auditLog->created_at->format('Y-m-d H:i:s') }}</dd>

                @if($auditLog->old_values)
                    <dt class="col-sm-3">Old Values</dt>
                    <dd class="col-sm-9"><pre>{{ json_encode($auditLog->old_values, JSON_PRETTY_PRINT) }}</pre></dd>
                @endif

                @if($auditLog->new_values)
                    <dt class="col-sm-3">New Values</dt>
                    <dd class="col-sm-9"><pre>{{ json_encode($auditLog->new_values, JSON_PRETTY_PRINT) }}</pre></dd>
                @endif
            </dl>

            <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">Back to Logs</a>
        </div>
    </div>
</div>
@endsection
