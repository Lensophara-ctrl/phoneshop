@extends('frontend.layouts.app')

@section('content')

<style>
    /* ========================================
       ULTRA MODERN DESIGN SYSTEM - 3X UPGRADE
       ======================================== */
    
    :root {
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --neon-blue: #00f0ff;
        --neon-purple: #b537f2;
        --neon-pink: #ff006e;
    }
    
    [data-bs-theme="dark"] {
        --glass-bg: rgba(0, 0, 0, 0.2);
        --glass-border: rgba(255, 255, 255, 0.1);
    }
    
    /* Advanced Animations */
    @keyframes float3d {
        0%, 100% { transform: translateY(0) rotateX(0deg); }
        50% { transform: translateY(-30px) rotateX(5deg); }
    }
    
    @keyframes neonPulse {
        0%, 100% { 
            box-shadow: 0 0 20px var(--neon-blue),
                        0 0 40px var(--neon-blue),
                        0 0 60px var(--neon-blue);
        }
        50% { 
            box-shadow: 0 0 30px var(--neon-purple),
                        0 0 60px var(--neon-purple),
                        0 0 90px var(--neon-purple);
        }
    }
    
    @keyframes gradientFlow {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(100px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes morphing {
        0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
        50% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
    }
    
    @keyframes glowRotate {
        0% { filter: hue-rotate(0deg); }
        100% { filter: hue-rotate(360deg); }
    }
    
    /* Glassmorphism Hero */
    .hero-glass {
        position: relative;
        min-height: 600px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
        background-size: 200% 200%;
        animation: gradientFlow 15s ease infinite;
        border-radius: 32px;
        overflow: hidden;
        margin-bottom: 4rem;
    }
    
    .hero-glass::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height