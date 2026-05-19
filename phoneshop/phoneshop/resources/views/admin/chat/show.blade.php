@extends('layouts.app')

@section('content')
<style>
    .chat-messages {
        height: calc(100vh - 350px);
        overflow-y: auto;
        padding: 1rem;
    }
    .chat-bubble {
        max-width: 80%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        margin-bottom: 0.75rem;
        word-wrap: break-word;
    }
    .chat-bubble.customer {
        background: var(--bs-primary);
        color: white;
        border-bottom-left-radius: 0.25rem;
        align-self: flex-start;
    }
    .chat-bubble.admin {
        background: var(--bs-tertiary-bg);
        border-bottom-right-radius: 0.25rem;
        align-self: flex-end;
    }
    .chat-container {
        display: flex;
        flex-direction: column;
    }
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.chat.index') }}" class="btn btn-outline-secondary btn-sm me-2">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
        <h2 class="fw-bold mb-0 d-inline">
            <i class="fa-solid fa-comment me-2"></i>{{ $conversation->customer_name }}
        </h2>
        <span class="badge bg-{{ $conversation->status == 'active' ? 'success' : ($conversation->status == 'resolved' ? 'warning' : 'secondary') }} ms-2">{{ ucfirst($conversation->status) }}</span>
    </div>
    <div class="d-flex gap-2">
        @if($conversation->status == 'active')
            <form action="{{ route('admin.chat.resolve', $conversation) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-outline-warning btn-sm" onclick="return confirm('Mark as resolved?')">
                    <i class="fa-solid fa-check-circle"></i> Resolve
                </button>
            </form>
            <form action="{{ route('admin.chat.close', $conversation) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-outline-danger btn-sm" onclick="return confirm('Close this conversation?')">
                    <i class="fa-solid fa-times-circle"></i> Close
                </button>
            </form>
        @else
            <form action="{{ route('admin.chat.reopen', $conversation) }}" method="POST" class="d-inline">
                @csrf
                <button class="btn btn-outline-primary btn-sm">
                    <i class="fa-solid fa-redo"></i> Reopen
                </button>
            </form>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body chat-container">
        <div class="chat-messages" id="chatMessages">
            @foreach($conversation->messages as $msg)
                <div class="d-flex {{ $msg->sender_type == 'admin' ? 'justify-content-end' : '' }}">
                    <div class="chat-bubble {{ $msg->sender_type }}">
                        <small class="d-block mb-1 opacity-75" style="font-size: 0.7rem;">
                            @if($msg->sender_type == 'admin')
                                @if($msg->is_bot)
                                    <i class="fa-solid fa-robot me-1"></i>Bot
                                @else
                                    {{ $msg->admin?->name ?? 'Staff' }}
                                @endif
                            @else
                                {{ $conversation->customer_name }}
                            @endif
                            &middot; {{ $msg->created_at->format('h:i A') }}
                        </small>
                        {{ $msg->message }}
                    </div>
                </div>
            @endforeach
        </div>

        <div class="border-top pt-3 mt-2">
            <form action="{{ route('admin.chat.reply', $conversation) }}" method="POST" id="replyForm" class="d-flex gap-2">
                @csrf
                <input type="text" name="message" class="form-control" placeholder="Type your reply..." required maxlength="1000" autocomplete="off">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-paper-plane"></i> Send
                </button>
            </form>
        </div>
    </div>
</div>

@if($conversation->customer_email)
    <div class="mt-3 text-muted small">
        <i class="fa-solid fa-envelope me-1"></i> {{ $conversation->customer_email }}
    </div>
@endif

<script>
    const chatMessages = document.getElementById('chatMessages');
    chatMessages.scrollTop = chatMessages.scrollHeight;

    document.getElementById('replyForm')?.addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';
    });
</script>
@endsection
