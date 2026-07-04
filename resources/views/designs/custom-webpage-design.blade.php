<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Donezo - Task Management Dashboard</title>
<!-- Tailwind CSS v3 CDN -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Lucide Icons for simplicity (replacing custom SVG paths where possible) -->
<script src="https://unpkg.com/lucide@latest"></script>
<!-- Google Fonts: Inter -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<style data-purpose="typography">
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f1f3f4;
    }
  </style>
<style data-purpose="custom-colors">
    /* Custom brand colors based on the image */
    .bg-brand-green { background-color: #216143; }
    .text-brand-green { color: #216143; }
    .border-brand-green { border-color: #216143; }
    .bg-brand-light-green { background-color: #eaf4ed; }
    .bg-brand-bright-green { background-color: #1aae6f; }
  </style>
<style data-purpose="layout-tweaks">
    .sidebar-item-active {
      background-color: #ffffff;
      border-left: 4px solid #216143;
      color: #216143;
    }
    .card-shadow {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    /* Simple custom pattern for the "Pending" bars and chart slices */
    .pattern-diagonal {
      background-image: repeating-linear-gradient(45deg, #d1d5db 0, #d1d5db 1px, transparent 0, transparent 50%);
      background-size: 8px 8px;
    }
    /* Time Tracker Background */
    .bg-tracker {
      background: linear-gradient(135deg, #062c1a 0%, #1aae6f 100%);
    }
  </style>
</head>
<body class="text-slate-700">
<div class="flex min-h-screen">
<!-- BEGIN: Sidebar -->
<aside class="w-64 bg-[#f8fafa] border-r border-gray-200 flex flex-col h-screen sticky top-0" data-purpose="sidebar">
<div class="p-8">
<div class="flex items-center gap-2 mb-10">
<div class="w-8 h-8 bg-brand-green rounded-lg flex items-center justify-center">
<svg class="text-white w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" viewbox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
</div>
<span class="text-xl font-bold tracking-tight text-slate-800">Donezo</span>
</div>
<nav class="space-y-1">
<p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-4">Menu</p>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl sidebar-item-active font-medium" href="#">
<i class="w-5 h-5" data-lucide="layout-grid"></i> Dashboard
          </a>
<a class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<div class="flex items-center gap-3">
<i class="w-5 h-5" data-lucide="check-square"></i> Tasks
            </div>
<span class="bg-brand-green text-white text-[10px] px-1.5 py-0.5 rounded">12+</span>
</a>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<i class="w-5 h-5" data-lucide="calendar"></i> Calendar
          </a>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<i class="w-5 h-5" data-lucide="bar-chart-2"></i> Analytics
          </a>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<i class="w-5 h-5" data-lucide="users"></i> Team
          </a>
</nav>
<nav class="space-y-1 mt-10">
<p class="text-[10px] uppercase font-bold text-gray-400 tracking-widest mb-4">General</p>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<i class="w-5 h-5" data-lucide="settings"></i> Settings
          </a>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-medium" href="#">
<i class="w-5 h-5" data-lucide="help-circle"></i> Help
          </a>
<a class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-500 hover:bg-red-50 font-medium mt-4" href="{{ route('absences.index') }}">
<i class="w-5 h-5" data-lucide="arrow-left"></i> Back to App
          </a>
</nav>
</div>
<!-- App Promo Card -->
<div class="mt-auto p-6">
<div class="bg-black text-white p-6 rounded-3xl relative overflow-hidden">
<div class="relative z-10">
<i class="mb-4 w-6 h-6" data-lucide="smartphone"></i>
<p class="font-bold text-sm leading-tight">Download our<br/>Mobile App</p>
<p class="text-[10px] text-gray-400 mt-2">Get easy in another way</p>
<button class="mt-4 bg-brand-bright-green text-white text-[10px] py-2 px-6 rounded-xl font-bold w-full">Download</button>
</div>
<!-- Abstract bg circle -->
<div class="absolute -bottom-10 -right-10 w-32 h-32 bg-brand-green rounded-full opacity-50 blur-2xl"></div>
</div>
</div>
</aside>
<!-- END: Sidebar -->
<!-- BEGIN: Main Content -->
<main class="flex-1 p-8 overflow-y-auto">
<!-- Top Header Row -->
<header class="flex justify-between items-center mb-10" data-purpose="top-nav">
<div class="relative w-1/2">
<i class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" data-lucide="search"></i>
<input class="w-full bg-white border-none rounded-2xl py-3.5 pl-12 pr-4 shadow-sm focus:ring-2 focus:ring-brand-green" placeholder="Search task" type="text"/>
<div class="absolute right-4 top-1/2 -translate-y-1/2 border border-gray-200 rounded px-1.5 py-0.5 text-[10px] text-gray-400 font-bold">⌘ F</div>
</div>
<div class="flex items-center gap-6">
<button class="w-12 h-12 bg-white rounded-full flex items-center justify-center border border-gray-100 shadow-sm relative">
<i class="w-5 h-5 text-gray-600" data-lucide="mail"></i>
</button>
<button class="w-12 h-12 bg-white rounded-full flex items-center justify-center border border-gray-100 shadow-sm relative">
<i class="w-5 h-5 text-gray-600" data-lucide="bell"></i>
<span class="absolute top-3 right-4 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
</button>
<div class="flex items-center gap-3">
<div class="text-right">
<p class="font-bold text-sm">Totok Michael</p>
<p class="text-xs text-gray-400">tmichael20@mail.com</p>
</div>
<img alt="User Avatar" class="w-12 h-12 rounded-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAYIutKQ2agD4PcYrq8liGshWA8BFvrLFT6JjdlVfjhmXF1tHP2tVOTcx_eqoJrhSKLAIXTZF1uy33zXcfUsCs5vliSJ3Ca7chqL3EfnKrESo2-qY5kcWCl71-UzTlOJaQ8ED-Ydpa6G4giY-UYPmQltNzDNC2n6sqTKT5rFYuw7vx8xa6qNxTIDrd7cCSETlMvBfObj1tbsgzSaFNXQ38ifpuYtblWfscg-l6E1Ue8AStjVkl6jIbal_6eqLVIUMciojHisxXS_0BN"/>
</div>
</div>
</header>
<!-- Dashboard Heading & Actions -->
<div class="flex justify-between items-end mb-8">
<div>
<h1 class="text-4xl font-bold text-slate-800">Dashboard</h1>
<p class="text-gray-400 mt-1">Plan, prioritize, and accomplish your tasks with ease.</p>
</div>
<div class="flex gap-3">
<button class="bg-brand-green text-white px-6 py-3 rounded-2xl font-semibold flex items-center gap-2 hover:bg-opacity-90 transition shadow-lg shadow-brand-green/20">
<i class="w-5 h-5" data-lucide="plus"></i> Add Project
          </button>
<button class="bg-white text-slate-800 border border-gray-200 px-6 py-3 rounded-2xl font-semibold hover:bg-gray-50 transition">
            Import Data
          </button>
</div>
</div>
<!-- Main Grid Layout -->
<div class="grid grid-cols-12 gap-6">
<!-- Stats Row -->
<div class="col-span-12 grid grid-cols-4 gap-6">
<!-- Total Projects -->
<div class="bg-brand-green text-white p-6 rounded-[2.5rem] card-shadow relative">
<div class="flex justify-between items-start mb-4">
<span class="text-sm font-medium">Total Projects</span>
<div class="w-10 h-10 bg-white rounded-full flex items-center justify-center">
<i class="w-5 h-5 text-black" data-lucide="arrow-up-right"></i>
</div>
</div>
<h2 class="text-5xl font-bold mb-4">24</h2>
<div class="bg-[#1a4a35] inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs">
<span class="bg-[#78c091] text-brand-green rounded-full px-1.5 py-0.5 text-[9px] font-bold">5</span>
              Increased from last month
            </div>
</div>
<!-- Ended Projects -->
<div class="bg-white p-6 rounded-[2.5rem] card-shadow relative border border-gray-50">
<div class="flex justify-between items-start mb-4">
<span class="text-sm font-medium text-slate-600">Ended Projects</span>
<div class="w-10 h-10 border border-gray-200 rounded-full flex items-center justify-center">
<i class="w-5 h-5 text-slate-800" data-lucide="arrow-up-right"></i>
</div>
</div>
<h2 class="text-5xl font-bold mb-4">10</h2>
<div class="bg-gray-50 inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs text-gray-400">
<span class="bg-brand-light-green text-brand-green rounded-full px-1.5 py-0.5 text-[9px] font-bold">6</span>
              Increased from last month
            </div>
</div>
<!-- Running Projects -->
<div class="bg-white p-6 rounded-[2.5rem] card-shadow relative border border-gray-50">
<div class="flex justify-between items-start mb-4">
<span class="text-sm font-medium text-slate-600">Running Projects</span>
<div class="w-10 h-10 border border-gray-200 rounded-full flex items-center justify-center">
<i class="w-5 h-5 text-slate-800" data-lucide="arrow-up-right"></i>
</div>
</div>
<h2 class="text-5xl font-bold mb-4">12</h2>
<div class="bg-gray-50 inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs text-gray-400">
<span class="bg-brand-light-green text-brand-green rounded-full px-1.5 py-0.5 text-[9px] font-bold">2</span>
              Increased from last month
            </div>
</div>
<!-- Pending Projects -->
<div class="bg-white p-6 rounded-[2.5rem] card-shadow relative border border-gray-50">
<div class="flex justify-between items-start mb-4">
<span class="text-sm font-medium text-slate-600">Pending Project</span>
<div class="w-10 h-10 border border-gray-200 rounded-full flex items-center justify-center">
<i class="w-5 h-5 text-slate-800" data-lucide="arrow-up-right"></i>
</div>
</div>
<h2 class="text-5xl font-bold mb-4">2</h2>
<p class="text-xs text-gray-400 mt-4 px-1">On Discuss</p>
</div>
</div>
<!-- Middle Row: Analytics & Reminders & Active Projects -->
<div class="col-span-8 grid grid-cols-2 gap-6">
<!-- Project Analytics -->
<div class="bg-white p-8 rounded-[2.5rem] card-shadow col-span-2 md:col-span-1">
<h3 class="text-lg font-bold mb-8">Project Analytics</h3>
<div class="flex items-end justify-between h-48 gap-3">
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-32 bg-gray-100 rounded-full pattern-diagonal"></div>
<span class="text-xs text-gray-400">S</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-36 bg-brand-green rounded-full"></div>
<span class="text-xs text-gray-400">M</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-24 bg-brand-bright-green/50 rounded-full relative flex flex-col items-center">
<div class="absolute -top-6 bg-white border border-gray-200 rounded-full px-2 py-0.5 text-[9px] text-brand-green font-bold shadow-sm">74%</div>
<div class="w-3 h-3 bg-white rounded-full mt-2 border-2 border-brand-bright-green"></div>
</div>
<span class="text-xs text-gray-400">T</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-44 bg-[#0a291b] rounded-full"></div>
<span class="text-xs text-gray-400">W</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-36 bg-gray-100 rounded-full pattern-diagonal"></div>
<span class="text-xs text-gray-400">T</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-32 bg-gray-100 rounded-full pattern-diagonal"></div>
<span class="text-xs text-gray-400">F</span>
</div>
<div class="flex flex-col items-center gap-3">
<div class="w-12 h-24 bg-gray-100 rounded-full pattern-diagonal"></div>
<span class="text-xs text-gray-400">S</span>
</div>
</div>
</div>
<!-- Reminders -->
<div class="bg-white p-8 rounded-[2.5rem] card-shadow">
<h3 class="text-lg font-bold mb-6">Reminders</h3>
<div class="space-y-4">
<div class="relative">
<h4 class="text-xl font-bold text-slate-800 leading-tight">Meeting with Arc Company</h4>
<p class="text-xs text-gray-400 mt-1">Time : 02.00 pm - 04.00 pm</p>
<button class="mt-8 bg-brand-green text-white px-6 py-4 rounded-2xl font-bold flex items-center justify-center gap-2 w-full shadow-lg shadow-brand-green/20">
<i class="w-5 h-5" data-lucide="video"></i> Start Meeting
                </button>
</div>
</div>
</div>
<!-- Team Collaboration -->
<div class="bg-white p-8 rounded-[2.5rem] card-shadow col-span-1">
<div class="flex justify-between items-center mb-6">
<h3 class="text-lg font-bold">Team Collaboration</h3>
<button class="text-[10px] font-bold border border-gray-200 rounded-full px-3 py-1.5">+ Add Member</button>
</div>
<div class="space-y-6">
<!-- Team Member -->
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<img alt="Alexandra" class="w-10 h-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAuQefd2_EheSXhgoMwAqT8Xv998WdWDBfAn90SqKM0XMOHcpEvpkqdmUztSUCL9uj5loJ6eoBdGMf2adjna8RPUx6n8jcc_u_xPBAE1VRt03FD4pqNqUpP14axaxt9-fKegwLlhetqmj_WFxdorZ_kLr6XJRTvRPyknNYMXtyYuIzs85s0WX72ePPz1dh6Um1rkUUgp13z_8IXExMGkbfhlR6JoUG8kR6g7WWOVWYV2U70ZUrtyLfOEFri9ZHpmUVtlqCqwDwicci2"/>
<div>
<p class="text-sm font-bold">Alexandra Deff</p>
<p class="text-[10px] text-gray-400">Working on <span class="text-slate-600 font-medium">Github Project Repository</span></p>
</div>
</div>
<span class="bg-brand-light-green text-brand-green text-[9px] font-bold px-2 py-0.5 rounded">Completed</span>
</div>
<!-- Team Member -->
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<img alt="Edwin" class="w-10 h-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCQpP5cvWhj9zZtBJd93xhX8gP_pSqviyER0ET3rFFo64iKiwoHtY99nGNE1DZnp-EnX-XglZ2X-CJDUwrT7he2ab-iXtiC7vjQ-BfNa7s0B9dfprcM0ayu-GEs3BapvyrTdGHepULgszQi5TZ55HSKaVZ2EnKDikg7Pfv9mMNB9e615atbHpGuYP1rjuKm0aUuXCg3DUNMSHdjO89V1fm-zwQyDs0DzEblE1eBf90hcLfCLH_SBaetQpH47W9hFzBdt2-biLUZEjX6"/>
<div>
<p class="text-sm font-bold">Edwin Adenike</p>
<p class="text-[10px] text-gray-400">Working on <span class="text-slate-600 font-medium">Integrate User Authentication System</span></p>
</div>
</div>
<span class="bg-orange-50 text-orange-400 text-[9px] font-bold px-2 py-0.5 rounded">In Progress</span>
</div>
<!-- Team Member -->
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<img alt="Isaac" class="w-10 h-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC-Wi4hKcn5nBfixP0B84g4B4KlZrSqAQKa65fBDFR5k_h6lCZ1uCLckxyDRy4U7MYp3Bk-s88oXuAUtqKDJBZKtrhsb9kVy648fJgFa_xfdAipzXC9j_JfUloVxkMsZxdCg5q2tP41JTTC_zOF-Xu8mUBjzMy-GGcd5KTWZPQl_uqViIvZUKqRZ9GmndY9qEEDrzPmyRibMtfp1-a6-S8uXwwHzun1Tuy0DKTnHbMOjbtm_TP-fHJSGS-a4MjJVaKk8IxX4d8sN2q7"/>
<div>
<p class="text-sm font-bold">Isaac Oluwatemilorun</p>
<p class="text-[10px] text-gray-400">Working on <span class="text-slate-600 font-medium">Develop Search and Filter Functionality</span></p>
</div>
</div>
<span class="bg-red-50 text-red-400 text-[9px] font-bold px-2 py-0.5 rounded">Pending</span>
</div>
<!-- Team Member -->
<div class="flex items-center justify-between">
<div class="flex items-center gap-3">
<img alt="David" class="w-10 h-10 rounded-full" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBKJk-zVAvV-th6Q5wKqRgyZvVrsaVxqZjjMDgOgMphjzCOVepTBK8YEWFFXIV44u8omgQjMAgd_LjRLpt_ufz56qnmQI-QfxNegitQlQqpeRmWul89RpCD1yBqBks2ORE_EAqczBh61TjZrvthEPQx2TYYRyDcALZUrULhZiBGRFF9mlYYCs_OaWggVpMbwkZTs4XIRbXsYjJqRWStoz7JdtDk-dSJQLgKfBMdKfOHi3Kon-WX4jiP20qzDrbuSugFQVXhJckqcy3O"/>
<div>
<p class="text-sm font-bold">David Oshodi</p>
<p class="text-[10px] text-gray-400">Working on <span class="text-slate-600 font-medium">Responsive Layout for Homepage</span></p>
</div>
</div>
<span class="bg-orange-50 text-orange-400 text-[9px] font-bold px-2 py-0.5 rounded">In Progress</span>
</div>
</div>
</div>
<!-- Project Progress -->
<div class="bg-white p-8 rounded-[2.5rem] card-shadow col-span-1 flex flex-col items-center">
<h3 class="text-lg font-bold w-full text-left mb-4">Project Progress</h3>
<div class="relative w-48 h-24 mt-4 overflow-hidden">
<!-- Simple Semi-circle Donut simulation -->
<div class="absolute inset-0 border-[24px] border-gray-100 rounded-t-full pattern-diagonal"></div>
<!-- Progress overlay -->
<div class="absolute inset-0 border-[24px] border-[#0a291b] border-r-0 border-b-0 rounded-t-full rotate-[147deg] origin-bottom"></div>
<div class="absolute inset-0 border-[24px] border-brand-green border-r-0 border-b-0 rounded-t-full rotate-45 origin-bottom"></div>
<!-- Center Text -->
<div class="absolute bottom-0 left-1/2 -translate-x-1/2 text-center">
<p class="text-4xl font-bold">41%</p>
<p class="text-[10px] text-gray-400 uppercase font-bold tracking-tight">Project Ended</p>
</div>
</div>
<div class="flex gap-4 mt-12">
<div class="flex items-center gap-1.5 text-[10px] font-medium text-gray-500">
<div class="w-2.5 h-2.5 bg-brand-bright-green rounded-full"></div> Completed
               </div>
<div class="flex items-center gap-1.5 text-[10px] font-medium text-gray-500">
<div class="w-2.5 h-2.5 bg-brand-green rounded-full"></div> In Progress
               </div>
<div class="flex items-center gap-1.5 text-[10px] font-medium text-gray-500">
<div class="w-2.5 h-2.5 bg-gray-200 rounded-full pattern-diagonal"></div> Pending
               </div>
</div>
</div>
<!-- Right Column: Projects & Timer -->
<div class="col-span-4 flex flex-col gap-6">
<!-- Active Projects List -->
<div class="bg-white p-8 rounded-[2.5rem] card-shadow flex-1">
<div class="flex justify-between items-center mb-8">
<h3 class="text-lg font-bold">Project</h3>
<button class="text-[10px] font-bold border border-gray-200 rounded-full px-3 py-1.5">+ New</button>
</div>
<div class="space-y-6">
<!-- Project Item -->
<div class="flex items-start gap-4">
<div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
<i class="w-5 h-5" data-lucide="code-2"></i>
</div>
<div class="flex-1">
<h4 class="text-sm font-bold">Develop API Endpoints</h4>
<p class="text-[10px] text-gray-400 mt-0.5">Due date: Nov 26, 2024</p>
</div>
</div>
<!-- Project Item -->
<div class="flex items-start gap-4">
<div class="w-10 h-10 bg-teal-50 rounded-xl flex items-center justify-center text-teal-500">
<i class="w-5 h-5" data-lucide="compass"></i>
</div>
<div class="flex-1">
<h4 class="text-sm font-bold">Onboarding Flow</h4>
<p class="text-[10px] text-gray-400 mt-0.5">Due date: Nov 28, 2024</p>
</div>
</div>
<!-- Project Item -->
<div class="flex items-start gap-4">
<div class="w-10 h-10 bg-orange-50 rounded-xl flex items-center justify-center text-orange-400">
<i class="w-5 h-5" data-lucide="layers"></i>
</div>
<div class="flex-1">
<h4 class="text-sm font-bold">Build Dashboard</h4>
<p class="text-[10px] text-gray-400 mt-0.5">Due date: Nov 30, 2024</p>
</div>
</div>
<!-- Project Item -->
<div class="flex items-start gap-4">
<div class="w-10 h-10 bg-yellow-50 rounded-xl flex items-center justify-center text-yellow-500">
<i class="w-5 h-5" data-lucide="zap"></i>
</div>
<div class="flex-1">
<h4 class="text-sm font-bold">Optimize Page Load</h4>
<p class="text-[10px] text-gray-400 mt-0.5">Due date: Dec 5, 2024</p>
</div>
</div>
<!-- Project Item -->
<div class="flex items-start gap-4">
<div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-500">
<i class="w-5 h-5" data-lucide="monitor"></i>
</div>
<div class="flex-1">
<h4 class="text-sm font-bold">Cross-Browser Testing</h4>
<p class="text-[10px] text-gray-400 mt-0.5">Due date: Dec 6, 2024</p>
</div>
</div>
</div>
</div>
<!-- Time Tracker Widget -->
<div class="bg-tracker p-8 rounded-[2.5rem] text-white card-shadow relative overflow-hidden h-72 flex flex-col justify-between">
<!-- Animated background effect -->
<div class="absolute inset-0 opacity-20 pointer-events-none">
<svg class="w-full h-full scale-150 -rotate-12" viewbox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
<path d="M0,200 Q100,50 200,200 T400,200" fill="none" opacity="0.3" stroke="white" stroke-width="20"></path>
<path d="M0,220 Q100,70 200,220 T400,220" fill="none" opacity="0.2" stroke="white" stroke-width="15"></path>
<path d="M0,240 Q100,90 200,240 T400,240" fill="none" opacity="0.1" stroke="white" stroke-width="10"></path>
</svg>
</div>
<div class="relative z-10">
<h3 class="text-lg font-medium opacity-80">Time Tracker</h3>
</div>
<div class="relative z-10 text-center mb-6">
<span class="text-5xl font-bold tabular-nums tracking-wider">01:24:08</span>
<div class="flex justify-center gap-4 mt-8">
<button class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white/30 transition">
<i class="w-6 h-6 fill-white" data-lucide="pause"></i>
</button>
<button class="w-14 h-14 bg-red-500 rounded-full flex items-center justify-center hover:bg-red-600 transition shadow-lg shadow-red-500/30">
<div class="w-4 h-4 bg-white rounded-sm"></div>
</button>
</div>
</div>
</div>
</div>
</div>
</main>
<!-- END: Main Content -->
</div>
<script data-purpose="lucide-initialization">
    // Initializing Lucide icons
    lucide.createIcons();
  </script>
</body></html>
