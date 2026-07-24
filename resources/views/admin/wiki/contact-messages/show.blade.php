@extends('layouts.admin')

@section('title', 'Message from ' . $message->name)

@section('content')
    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">{{ $message->subject }}</h1>
            <a href="{{ route('admin.wiki.contact-messages.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Back to messages
            </a>
        </div>

        <div class="card" style="max-width:720px;">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-2">From</dt>
                    <dd class="col-sm-10">{{ $message->name }} &lt;{{ $message->email }}&gt;</dd>

                    <dt class="col-sm-2">Received</dt>
                    <dd class="col-sm-10">{{ $message->created_at->format('d F Y, H:i') }}</dd>

                    <dt class="col-sm-2">Message</dt>
                    <dd class="col-sm-10" style="white-space: pre-wrap;">{{ $message->message }}</dd>
                </dl>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-reply"></i> Reply by email
                </a>
                <form method="POST" action="{{ route('admin.wiki.contact-messages.destroy', $message) }}"
                      onsubmit="return confirm('Delete this message?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
