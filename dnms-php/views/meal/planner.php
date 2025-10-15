<?php $pageTitle = 'Meal Planner'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - NutriTrack Pro</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Enhanced DNMS Styling with Advanced Design */
        :root {
            /* Unified DNMS Color System - Sky Blue Theme */
            --dnms-primary: #0ea5e9;          /* Sky blue */
            --dnms-primary-dark: #0284c7;     /* Darker sky blue for hover */
            --dnms-primary-light: #7dd3fc;    /* Lighter sky blue for accents */
            --dnms-secondary: #06b6d4;        /* Cyan for success */
            --dnms-warning: #f59e0b;          /* Warning orange */
            --dnms-danger: #ef4444;           /* Error red */
            --dnms-info: #3b82f6;             /* Info blue */
            --nutrition-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #7dd3fc 100%);
            --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --success-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --glass-gradient: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
            --rainbow-gradient: linear-gradient(90deg, #0ea5e9, #7dd3fc, #06b6d4, #3b82f6, #0ea5e9);

            /* Enhanced Light Theme */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 25px rgba(0, 0, 0, 0.15);
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.1);
            --navbar-bg: rgba(255, 255, 255, 0.95);
            --sidebar-bg: #f8fafc;
            --accent-color: #0ea5e9;
            --accent-hover: #0284c7;
            --success-color: #06b6d4;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
        }

        /* Enhanced Dark Theme */
        [data-theme="dark"] {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-card: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 8px 25px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 20px 40px rgba(0, 0, 0, 0.3);
            --navbar-bg: rgba(15, 23, 42, 0.95);
            --sidebar-bg: #1e293b;
            --accent-color: #7dd3fc;
            --accent-hover: #0ea5e9;
            --success-color: #7dd3fc;
            --warning-color: #fbbf24;
            --error-color: #f87171;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            transition: all 0.3s ease;
            position: relative;
        }

        /* Animated Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 20% 50%, rgba(14, 165, 233, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 80% 20%, rgba(6, 182, 212, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 40% 80%, rgba(14, 165, 233, 0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: -1;
            transition: opacity 0.3s ease;
        }

        [data-theme="dark"] body::before {
            opacity: 0.8;
        }

        /* Enhanced Top Header */
        .top-header {
            background: linear-gradient(135deg, var(--navbar-bg) 0%, rgba(255, 255, 255, 0.9) 100%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        [data-theme="dark"] .top-header {
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.95) 0%, rgba(30, 41, 59, 0.9) 100%);
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .menu-toggle {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 255, 255, 0.1) 100%);
            border: 2px solid var(--border-color);
            border-radius: 12px;
            padding: 0.75rem;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 45px;
            height: 45px;
            backdrop-filter: blur(10px);
        }

        .menu-toggle:hover {
            background: var(--nutrition-gradient);
            border-color: transparent;
            color: white;
            transform: scale(1.05) rotate(90deg);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
        }

        .brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.5rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.2);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
        }

        .user-name {
            color: var(--text-secondary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 255, 255, 0.05) 100%);
            border-radius: 8px;
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .user-name:hover {
            background: var(--nutrition-gradient);
            color: white;
            border-color: transparent;
        }

        /* Theme Toggle */
        .theme-toggle {
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(255, 255, 255, 0.1) 100%);
            border: 2px solid var(--border-color);
            border-radius: 50px;
            padding: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }

        .theme-toggle:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.2);
            border-color: var(--nutrition-gradient);
        }

        .theme-toggle i {
            font-size: 1.125rem;
            color: var(--text-secondary);
            transition: color 0.3s ease;
        }

        .toggle-ball {
            width: 24px;
            height: 24px;
            background: var(--nutrition-gradient);
            border-radius: 50%;
            transition: transform 0.3s ease;
            position: relative;
            box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
        }

        [data-theme="dark"] .toggle-ball {
            transform: translateX(24px);
        }

        /* Enhanced Sidebar */
        .sidebar {
            width: 280px;
            background: var(--sidebar-bg);
            position: fixed;
            left: 0;
            top: 80px;
            height: calc(100vh - 80px);
            overflow-y: auto;
            border-right: 1px solid var(--border-color);
            transition: transform 0.3s ease, background-color 0.3s ease;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .sidebar-nav {
            padding: 2rem 1rem;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 0.5rem;
            border-radius: 12px;
            text-decoration: none;
            color: var(--text-secondary);
            font-weight: 500;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: var(--nutrition-gradient);
            transition: left 0.3s ease;
            z-index: -1;
            opacity: 0.1;
        }

        .nav-item:hover::before {
            left: 0;
        }

        .nav-item:hover {
            color: var(--text-primary);
            background: rgba(14, 165, 233, 0.1);
            transform: translateX(8px);
            box-shadow: var(--shadow-sm);
        }

        .nav-item.active {
            background: var(--nutrition-gradient);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .nav-item.active::before {
            left: 0;
            opacity: 0.2;
        }

        .nav-item i {
            font-size: 1.2rem;
            width: 20px;
            text-align: center;
        }

        /* Enhanced Main Content */
        .main-content {
            margin-left: 280px;
            margin-top: 80px;
            padding: 2rem;
            min-height: calc(100vh - 80px);
            background: var(--bg-primary);
            transition: background-color 0.3s ease;
        }

        /* Enhanced Meal Planner Styling */
        .meal-planner-page {
            max-width: 1600px;
            margin: 0 auto;
            padding: 2rem 1rem;
            position: relative;
        }

        .planner-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
        }

        .planner-title {
            font-size: 3.5rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
            animation: fadeInUp 0.8s ease-out;
            position: relative;
        }

        .planner-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: var(--nutrition-gradient);
            border-radius: 2px;
        }

        .planner-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
            animation: fadeInUp 0.8s ease-out 0.2s both;
        }

        /* Enhanced Quick Actions */
        .quick-actions {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
            padding: 1rem;
        }

        .action-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.1) 100%);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 2px solid transparent;
            border-image: linear-gradient(135deg, rgba(37, 99, 235, 0.3), rgba(52, 211, 153, 0.3), rgba(37, 99, 235, 0.3)) 1;
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 220px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1),
                       inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--rainbow-gradient);
            opacity: 0.8;
        }

        .action-card::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(37, 99, 235, 0.1) 0%, transparent 70%);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.4s ease, height 0.4s ease;
            z-index: 0;
        }

        .action-card:hover::after {
            width: 300px;
            height: 300px;
        }

        .action-card:hover {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.25),
                       inset 0 1px 0 rgba(255, 255, 255, 0.3);
            border-image: linear-gradient(135deg, rgba(37, 99, 235, 0.6), rgba(52, 211, 153, 0.6), rgba(37, 99, 235, 0.6)) 1;
        }

        .action-card > * {
            position: relative;
            z-index: 1;
        }

        .action-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--nutrition-gradient) 0%, var(--primary-gradient) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            color: white;
            font-size: 1.8rem;
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
            transition: all 0.3s ease;
        }

        .action-card:hover .action-icon {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 0 12px 35px rgba(37, 99, 235, 0.4);
        }

        .action-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .action-desc {
            font-size: 0.95rem;
            color: var(--text-secondary);
            line-height: 1.5;
            opacity: 0.9;
        }

        .planner-controls {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .date-nav {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: var(--bg-card);
            padding: 1.5rem 2rem;
            border-radius: 20px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-color);
            backdrop-filter: blur(10px);
        }

        .date-nav-btn {
            background: var(--nutrition-gradient);
            border: none;
            color: white;
            padding: 1rem;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.2rem;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .date-nav-btn:hover {
            transform: scale(1.1);
            box-shadow: var(--shadow-md);
        }

        .current-date {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            min-width: 250px;
            text-align: center;
            padding: 0.5rem 1rem;
            background: var(--bg-secondary);
            border-radius: 12px;
            border: 1px solid var(--border-color);
        }

        .week-view {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .day-card {
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(255, 255, 255, 0.02) 100%);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1),
                       inset 0 1px 0 rgba(255, 255, 255, 0.1);
            border: 2px solid transparent;
            border-image: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(52, 211, 153, 0.2), rgba(37, 99, 235, 0.2)) 1;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            animation: slideInUp 0.6s ease-out;
        }

        .day-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 6px;
            background: var(--rainbow-gradient);
            opacity: 0.8;
        }

        .day-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at 50% 0%, rgba(37, 99, 235, 0.05) 0%, transparent 50%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .day-card:hover {
            transform: translateY(-12px) scale(1.02);
            box-shadow: 0 20px 60px rgba(37, 99, 235, 0.2),
                       inset 0 1px 0 rgba(255, 255, 255, 0.2);
            border-image: linear-gradient(135deg, rgba(37, 99, 235, 0.5), rgba(52, 211, 153, 0.5), rgba(37, 99, 235, 0.5)) 1;
        }

        .day-card:hover::after {
            opacity: 1;
        }

        .day-card.today {
            border: 2px solid var(--nutrition-gradient);
            background: linear-gradient(135deg, var(--bg-card) 0%, rgba(37, 99, 235, 0.08) 100%);
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.15),
                       inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .day-card.today::before {
            background: var(--nutrition-gradient);
            height: 8px;
        }

        .day-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            position: relative;
        }

        .day-name::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 30px;
            height: 2px;
            background: var(--nutrition-gradient);
            border-radius: 1px;
        }

        .day-date {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--rainbow-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 2rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .day-card.today .day-date {
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .day-meals {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .meal-slot {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, rgba(255, 255, 255, 0.05) 100%);
            border: 2px dashed transparent;
            border-image: linear-gradient(135deg, var(--border-color), rgba(37, 99, 235, 0.3), var(--border-color)) 1;
            border-radius: 14px;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.1);
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 0.75rem;
        }

        .meal-slot::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(37, 99, 235, 0.08) 0%, rgba(52, 211, 153, 0.08) 100%);
            transition: left 0.4s ease;
            z-index: 0;
        }

        .meal-slot::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(circle at center, rgba(37, 99, 235, 0.03) 0%, transparent 70%);
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .meal-slot:hover::before {
            left: 0;
        }

        .meal-slot:hover::after {
            opacity: 1;
        }

        .meal-slot:hover {
            background: linear-gradient(135deg, var(--bg-tertiary) 0%, rgba(37, 99, 235, 0.08) 100%);
            border-image: linear-gradient(135deg, var(--nutrition-gradient), rgba(52, 211, 153, 0.6), var(--nutrition-gradient)) 1;
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 40px rgba(37, 99, 235, 0.15),
                       inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }

        .meal-slot:active {
            transform: translateY(-2px) scale(0.98);
        }

        .meal-slot > * {
            position: relative;
            z-index: 1;
        }

        .meal-slot h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .meal-slot .meal-name {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            min-height: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            line-height: 1.3;
        }

        .meal-slot .meal-calories {
            font-size: 0.8rem;
            color: var(--accent-color);
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .meal-slot .meal-macros {
            font-size: 0.7rem;
            color: var(--text-secondary);
            opacity: 0.8;
            line-height: 1.2;
        }

        .meal-slot.planned {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
            border: 2px solid var(--accent-color);
        }

        .meal-slot.planned::before {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
        }

        .meal-slot.planned .meal-name {
            color: var(--accent-color);
        }

        .add-meal-btn {
            background: var(--nutrition-gradient);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
            width: 100%;
            font-size: 1rem;
            position: relative;
            overflow: hidden;
        }

        .add-meal-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.3s ease, height 0.3s ease;
        }

        .add-meal-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .add-meal-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.3);
        }

        /* Enhanced Stats Section */
        .planner-stats {
            background: var(--glass-gradient);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid transparent;
            border-image: linear-gradient(135deg, rgba(37, 99, 235, 0.2), rgba(52, 211, 153, 0.2), rgba(37, 99, 235, 0.2)) 1;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 3rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1),
                       inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .stat-card {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(37, 99, 235, 0.15);
            border-color: rgba(37, 99, 235, 0.3);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            background: var(--nutrition-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 1.1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .week-view {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .day-card {
                padding: 1.75rem;
            }

            .meal-slot {
                padding: 1.25rem;
                min-height: 120px;
            }

            .meal-slot h4 {
                font-size: 0.9rem;
            }

            .meal-slot .meal-name {
                font-size: 1rem;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Top Header -->
    <header class="top-header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand">
                <span class="brand-name">NutriTrack Pro</span>
                <span class="brand-module">DNMS</span>
            </div>
        </div>
        <div class="header-right">
            <a href="../../dashboard.php" class="btn btn-primary btn-sm">🏠 Main Dashboard</a>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="../../index.php" class="nav-item">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="../../views/food/search.php" class="nav-item">
                <i class="fas fa-search"></i>
                <span>Food Search</span>
            </a>
            <a href="planner.php" class="nav-item active">
                <i class="fas fa-calendar-alt"></i>
                <span>Meal Planner</span>
            </a>
            <a href="log.php" class="nav-item">
                <i class="fas fa-book"></i>
                <span>Meal Log</span>
            </a>
            <a href="history.php" class="nav-item">
                <i class="fas fa-history"></i>
                <span>Meal History</span>
            </a>
            <a href="../../views/goals/index.php" class="nav-item">
                <i class="fas fa-bullseye"></i>
                <span>Goals</span>
            </a>
            <a href="../../views/reports/index.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Reports</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="meal-planner-page">
            <div class="planner-header">
                <h1 class="planner-title">📅 Advanced Meal Planner</h1>
                <p class="planner-subtitle">Plan your meals for the week and stay on track with your nutrition goals</p>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <div class="action-card" onclick="generateWeeklyPlan()">
                    <div class="action-icon">
                        <i class="fas fa-magic"></i>
                    </div>
                    <div class="action-title">AI Meal Plan</div>
                    <div class="action-desc">Generate personalized meal plan</div>
                </div>
                <div class="action-card" onclick="importFromHistory()">
                    <div class="action-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <div class="action-title">Import from History</div>
                    <div class="action-desc">Use previous meal patterns</div>
                </div>
                <div class="action-card" onclick="createCustomMeal()">
                    <div class="action-icon">
                        <i class="fas fa-plus"></i>
                    </div>
                    <div class="action-title">Custom Meal</div>
                    <div class="action-desc">Create your own meal plan</div>
                </div>
            </div>

            <!-- Planner Stats -->
            <div class="planner-stats">
                <div class="stat-card">
                    <div class="stat-value" id="weeklyCalories">0</div>
                    <div class="stat-label">Weekly Calories</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="dailyAverage">0</div>
                    <div class="stat-label">Daily Average</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="mealsPlanned">0</div>
                    <div class="stat-label">Meals Planned</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="completionRate">0%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
            </div>

            <div class="planner-controls">
                <div class="date-nav">
                    <button class="date-nav-btn" onclick="changeWeek(-1)">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <div class="current-date" id="currentDate">
                        <?php echo date('M j, Y'); ?>
                    </div>
                    <button class="date-nav-btn" onclick="changeWeek(1)">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            <div class="week-view">
                <?php
                $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $today = date('Y-m-d');
                $startOfWeek = date('Y-m-d', strtotime('monday this week', strtotime($today)));

                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime("+$i days", strtotime($startOfWeek)));
                    $dayName = $days[$i];
                    $dayDate = date('j', strtotime($date));
                    $isToday = ($date === $today) ? 'today' : '';
                ?>
                <div class="day-card <?php echo $isToday; ?>">
                    <div class="day-name"><?php echo $dayName; ?></div>
                    <div class="day-date"><?php echo $dayDate; ?></div>

                    <div class="day-meals">
                        <div class="meal-slot" onclick="addMeal('<?php echo $date; ?>', 'breakfast')" data-meal="breakfast">
                            <h4>🌅 Breakfast</h4>
                            <div class="meal-name">No meal planned</div>
                            <div class="meal-calories">0 kcal</div>
                            <div class="meal-macros">0g P • 0g C • 0g F</div>
                        </div>

                        <div class="meal-slot" onclick="addMeal('<?php echo $date; ?>', 'lunch')" data-meal="lunch">
                            <h4>☀️ Lunch</h4>
                            <div class="meal-name">No meal planned</div>
                            <div class="meal-calories">0 kcal</div>
                            <div class="meal-macros">0g P • 0g C • 0g F</div>
                        </div>

                        <div class="meal-slot" onclick="addMeal('<?php echo $date; ?>', 'dinner')" data-meal="dinner">
                            <h4>🌙 Dinner</h4>
                            <div class="meal-name">No meal planned</div>
                            <div class="meal-calories">0 kcal</div>
                            <div class="meal-macros">0g P • 0g C • 0g F</div>
                        </div>

                        <div class="meal-slot" onclick="addMeal('<?php echo $date; ?>', 'snacks')" data-meal="snacks">
                            <h4>🍎 Snacks</h4>
                            <div class="meal-name">No snacks planned</div>
                            <div class="meal-calories">0 kcal</div>
                            <div class="meal-macros">0g P • 0g C • 0g F</div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </main>

    <script>
        // Sidebar toggle functionality
        document.getElementById('menuToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Set active nav item
        const currentPath = window.location.href;
        document.querySelectorAll('.nav-item').forEach(item => {
            if (item.href === currentPath) {
                document.querySelectorAll('.nav-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });

        function changeWeek(direction) {
            const currentDateElement = document.getElementById('currentDate');
            if (!currentDateElement) return;

            try {
                // Parse the current date text (format: "Dec 15, 2024")
                const dateText = currentDateElement.textContent.trim();
                const currentDate = new Date(dateText);

                if (isNaN(currentDate.getTime())) {
                    console.error('Invalid date format:', dateText);
                    return;
                }

                currentDate.setDate(currentDate.getDate() + (direction * 7));
                currentDateElement.textContent = currentDate.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric'
                });

                updateWeekStats();
            } catch (error) {
                console.error('Error changing week:', error);
            }
        }

        function addMeal(date, mealType) {
            try {
                // In a real implementation, this would open a modal or redirect to add meal
                const modal = createMealModal(date, mealType);
                if (modal) {
                    document.body.appendChild(modal);
                    modal.style.display = 'flex';
                }
            } catch (error) {
                console.error('Error creating meal modal:', error);
                showNotification('Error opening meal planner', 'error');
            }
        }

        function createMealModal(date, mealType) {
            try {
                const modal = document.createElement('div');
                modal.className = 'meal-modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Add ${mealType.charAt(0).toUpperCase() + mealType.slice(1)} for ${new Date(date).toLocaleDateString()}</h3>
                            <button class="modal-close" onclick="closeModal(this)" title="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Meal planning interface would go here...</p>
                        </div>
                    </div>
                `;

                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.5);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10001;
                    backdrop-filter: blur(5px);
                    opacity: 0;
                    transition: opacity 0.3s ease;
                `;

                // Add modal styles
                const modalStyles = document.createElement('style');
                modalStyles.textContent = `
                    .modal-content {
                        background: var(--bg-card);
                        border-radius: 16px;
                        padding: 2rem;
                        max-width: 500px;
                        width: 90%;
                        max-height: 80vh;
                        overflow-y: auto;
                        border: 1px solid var(--border-color);
                        box-shadow: var(--shadow-xl);
                    }
                    .modal-header {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        margin-bottom: 1.5rem;
                        padding-bottom: 1rem;
                        border-bottom: 1px solid var(--border-color);
                    }
                    .modal-header h3 {
                        margin: 0;
                        color: var(--text-primary);
                        font-size: 1.25rem;
                        font-weight: 600;
                    }
                    .modal-close {
                        background: var(--bg-secondary);
                        border: none;
                        border-radius: 50%;
                        width: 32px;
                        height: 32px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        color: var(--text-secondary);
                        transition: all 0.3s ease;
                    }
                    .modal-close:hover {
                        background: var(--danger-gradient);
                        color: white;
                    }
                    .modal-body {
                        color: var(--text-secondary);
                        line-height: 1.6;
                    }
                `;

                if (!document.querySelector('#modal-styles')) {
                    modalStyles.id = 'modal-styles';
                    document.head.appendChild(modalStyles);
                }

                // Fade in modal
                setTimeout(() => {
                    modal.style.opacity = '1';
                }, 10);

                return modal;
            } catch (error) {
                console.error('Error creating modal:', error);
                return null;
            }
        }

        function closeModal(button) {
            const modal = button.closest('.meal-modal');
            if (modal) {
                modal.remove();
            }
        }

        function generateWeeklyPlan() {
            showNotification('AI meal plan generation coming soon!', 'info');
        }

        function importFromHistory() {
            showNotification('Import from history feature coming soon!', 'info');
        }

        function createCustomMeal() {
            showNotification('Custom meal creation coming soon!', 'info');
        }

        function updateWeekStats() {
            // Simulate updating stats with error handling
            const elements = {
                weeklyCalories: document.getElementById('weeklyCalories'),
                dailyAverage: document.getElementById('dailyAverage'),
                mealsPlanned: document.getElementById('mealsPlanned'),
                completionRate: document.getElementById('completionRate')
            };

            // Check if all elements exist before updating
            const allElementsExist = Object.values(elements).every(el => el !== null);
            if (!allElementsExist) {
                console.warn('Some stat elements not found, skipping stats update');
                return;
            }

            try {
                elements.weeklyCalories.textContent = '14,250';
                elements.dailyAverage.textContent = '2,036';
                elements.mealsPlanned.textContent = '21';
                elements.completionRate.textContent = '75%';
            } catch (error) {
                console.error('Error updating week stats:', error);
            }
        }

        // Theme Management
        function toggleTheme() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);

            // Update toggle button
            const toggleBall = document.querySelector('.toggle-ball');
            if (newTheme === 'dark') {
                toggleBall.style.transform = 'translateX(24px)';
            } else {
                toggleBall.style.transform = 'translateX(0)';
            }

            showNotification(`Switched to ${newTheme} mode!`, 'success');
        }

        function initializeTheme() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);

            // Update toggle button on load
            const toggleBall = document.querySelector('.toggle-ball');
            if (savedTheme === 'dark') {
                toggleBall.style.transform = 'translateX(24px)';
            }
        }

        function showNotification(message, type = 'info') {
            try {
                const notification = document.createElement('div');
                const colors = {
                    success: 'linear-gradient(135deg, #10b981 0%, #059669 100%)',
                    error: 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)',
                    warning: 'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
                    info: 'linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%)'
                };

                notification.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    background: ${colors[type] || colors.info};
                    color: white;
                    padding: 1rem 1.5rem;
                    border-radius: 12px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                    z-index: 10001;
                    max-width: 350px;
                    font-family: 'Inter', sans-serif;
                    animation: slideInRight 0.3s ease;
                    font-weight: 500;
                `;

                notification.innerHTML = `
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-${getIcon(type)}" style="font-size: 18px;"></i>
                        <p style="margin: 0; font-size: 14px; line-height: 1.4;">${message}</p>
                    </div>
                `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.style.animation = 'slideInRight 0.3s ease reverse';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }
                }, 4000);
            } catch (error) {
                console.error('Error showing notification:', error);
            }
        }

        function getIcon(type) {
            const icons = {
                success: 'check-circle',
                error: 'exclamation-circle',
                warning: 'exclamation-triangle',
                info: 'info-circle'
            };
            return icons[type] || 'info-circle';
        }

        // Initialize theme and stats on page load
        document.addEventListener('DOMContentLoaded', function() {
            initializeTheme();
            updateWeekStats();

            // Add CSS animations for notifications
            const notificationStyles = document.createElement('style');
            notificationStyles.textContent = `
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100%);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            `;
            document.head.appendChild(notificationStyles);
        });
    </script>
</body>
</html>
