@extends('layouts.app')

@section('title', 'Upgrade to Premium - CloudBox')
@section('page-title', 'Premium Upgrade')

@section('content')
<div class="max-w-5xl mx-auto space-y-10">
    
    <!-- Hero Header -->
    <div class="text-center space-y-3">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold uppercase tracking-wider shadow-xs">
            ⚡ Instant Account Storage Expansion
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold font-heading text-slate-900">
            Upgrade to 5 GB Cloud Storage
        </h2>
        <p class="text-sm text-slate-500 max-w-lg mx-auto">
            Scan ABA Bank KHQR Code to instantly unlock 5 GB storage and 100 MB single file uploads.
        </p>
    </div>

    <!-- Hidden ABA Merchant Purchase Form -->
    <form method="POST" target="aba_webservice"
        action="https://checkout-sandbox.payway.com.kh/api/payment-gateway/v1/payments/purchase"
        id="aba_merchant_request">
        @csrf
        <input type="hidden" name="hash" value="{{ $hash ?? '' }}" id="hash" />
        <input type="hidden" name="tran_id" value="{{ $tranId ?? '' }}" id="tran_id" />
        <input type="hidden" name="amount" value="{{ $amount ?? '' }}" id="amount" />
        <input type="hidden" name="payment_option" value="{{ $payment_option ?? 'abapay_khqr' }}" />
        <input type="hidden" name="merchant_id" value="{{ $merchant_id ?? '' }}" />
        <input type="hidden" name="req_time" value="{{ $req_time ?? '' }}" />
        <input type="hidden" name="continue_success_url" value="{{ $continue_success_url ?? '' }}" />
        <input type="hidden" name="currency" value="{{ $currency ?? 'USD' }}" />
    </form>

    <!-- Comparison Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Free Plan Card -->
        <div class="clean-card p-8 rounded-3xl flex flex-col justify-between hover:border-slate-300 transition">
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">Free Plan</span>
                    <span class="px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold font-mono border border-slate-200">FREE</span>
                </div>
                <div>
                    <h3 class="text-3xl font-extrabold font-heading text-slate-900">$0.00 <span class="text-xs font-normal text-slate-500">/ lifetime</span></h3>
                    <p class="text-xs text-slate-500 mt-1">Standard Storage Quota</p>
                </div>
                <ul class="space-y-3 text-xs text-slate-600 pt-4 border-t border-slate-100">
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Maximum storage limit: <strong>100 MB</strong>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Single upload limit: <strong>10 MB per file</strong>
                    </li>
                    <li class="flex items-center gap-3 text-slate-400">
                        <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Priority file processing
                    </li>
                </ul>
            </div>
            <div class="mt-8 pt-4 text-center">
                @if(!$user->is_premium)
                    <span class="text-xs text-slate-400 font-semibold">Currently Active Plan</span>
                @endif
            </div>
        </div>

        <!-- Premium Pro Plan Card -->
        <div class="bg-gradient-to-b from-amber-50/60 via-white to-white p-8 rounded-3xl border-2 border-amber-400 relative overflow-hidden flex flex-col justify-between shadow-xl shadow-amber-500/10">
            <div class="space-y-6 z-10">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-amber-700 uppercase tracking-widest">Premium Plan</span>
                    <span class="px-3 py-1 rounded-full bg-gradient-to-r from-amber-500 to-yellow-500 text-slate-950 text-xs font-extrabold font-heading shadow-xs">
                        PREMIUM PLAN
                    </span>
                </div>
                <div>
                    <h3 class="text-4xl font-extrabold font-heading text-slate-900">$5.00 <span class="text-xs font-normal text-amber-700">/ one-time payment</span></h3>
                    <p class="text-xs text-amber-700/80 mt-1">Lifetime Storage Expansion</p>
                </div>
                <ul class="space-y-3 text-xs text-slate-700 pt-4 border-t border-amber-200/80">
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Maximum storage limit: <strong>5 GB Capacity (50x larger)</strong>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Single upload limit: <strong>100 MB per file</strong>
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        ABA Bank KHQR Code Instant Payment
                    </li>
                    <li class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Instant Automatic Account Activation
                    </li>
                </ul>
            </div>

            <div class="mt-8 z-10 space-y-3">
                @if($user->is_premium)
                    <div class="w-full py-3.5 rounded-2xl bg-amber-100 text-amber-900 text-center font-bold text-sm border border-amber-300">
                        ✓ Account Active as Premium User (5 GB Storage)
                    </div>
                @else
                    <!-- ABA PayWay Checkout Button -->
                    <button type="button" onclick="handleAbaCheckout()" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-red-600 via-red-500 to-amber-500 hover:from-red-700 hover:to-amber-600 text-white font-extrabold text-sm transition shadow-lg shadow-red-500/20 flex items-center justify-center gap-2.5 cursor-pointer">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Pay $5.00 via ABA PayWay KHQR
                    </button>
                @endif
            </div>
        </div>
    </div>

</div>

<!-- ================= ABA PAYWAY MODAL ================= -->
<div id="abaModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-md hidden p-4 overflow-y-auto">
    <div class="w-full max-w-sm bg-white rounded-3xl border border-slate-200 shadow-2xl overflow-hidden relative text-slate-900">
        
        <!-- Header -->
        <div class="bg-red-700 p-5 text-white text-center relative">
            <button onclick="closeModal('abaModal')" class="absolute top-4 right-4 text-white/70 hover:text-white p-1 rounded-full hover:bg-white/10 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            <div class="inline-flex items-center gap-2 font-bold text-lg tracking-wider font-heading">
                <span class="px-2 py-0.5 rounded text-[10px] font-black bg-white text-red-700">KHQR</span>
                ABA PayWay
            </div>
            <p class="text-xs text-red-100 mt-1">CloudBox Premium Account Upgrade</p>
        </div>

        <!-- Body / QR Display -->
        <div class="p-6 text-center space-y-4 bg-slate-50">
            <!-- Merchant Info -->
            <div class="bg-white p-4 rounded-2xl border border-slate-200 text-left space-y-1 shadow-xs">
                <div class="text-[10px] text-slate-400 uppercase tracking-widest font-bold">Merchant Name</div>
                <div class="text-sm font-bold text-slate-900 font-heading">CloudBox Storage Inc.</div>
                <div class="flex justify-between items-center pt-2.5 border-t border-slate-100">
                    <span class="text-xs text-slate-500">Total Price</span>
                    <span class="text-xl font-extrabold font-heading text-amber-600">$5.00 <span class="text-xs text-slate-500 font-normal font-sans">USD</span></span>
                </div>
            </div>

            <!-- KHQR Display Box -->
            <div class="relative w-64 h-64 mx-auto p-4 bg-white rounded-3xl shadow-xl flex flex-col items-center justify-center border-4 border-red-600">
                <div class="w-full flex items-center justify-between px-2 mb-2">
                    <span class="text-[11px] font-black text-red-600">KHQR</span>
                    <span class="text-[10px] font-bold text-blue-900 font-heading">ABA BANK</span>
                </div>
                
                <!-- QR Graphic -->
                <div class="w-44 h-44 bg-slate-950 p-2 rounded-xl flex items-center justify-center relative">
                    <svg viewBox="0 0 100 100" class="w-full h-full text-white fill-current">
                        <path d="M 5 5 h 25 v 25 h -25 z M 10 10 v 15 h 15 v -15 z M 15 15 h 5 v 5 h -5 z" />
                        <path d="M 70 5 h 25 v 25 h -25 z M 75 10 v 15 h 15 v -15 z M 80 15 h 5 v 5 h -5 z" />
                        <path d="M 5 70 h 25 v 25 h -25 z M 10 75 v 15 h 15 v -15 z M 15 80 h 5 v 5 h -5 z" />
                        <rect x="35" y="10" width="8" height="8" />
                        <rect x="50" y="10" width="12" height="6" />
                        <rect x="35" y="25" width="6" height="12" />
                        <rect x="45" y="22" width="10" height="10" />
                        <rect x="10" y="35" width="15" height="6" />
                        <rect x="10" y="45" width="8" height="18" />
                        <rect x="30" y="40" width="12" height="12" />
                        <rect x="50" y="40" width="15" height="15" />
                        <rect x="70" y="35" width="20" height="8" />
                        <rect x="70" y="50" width="12" height="12" />
                        <rect x="35" y="60" width="10" height="20" />
                        <rect x="55" y="60" width="15" height="10" />
                        <rect x="75" y="70" width="18" height="18" />
                        <rect x="50" y="80" width="18" height="12" />
                        <rect x="40" y="40" width="20" height="20" rx="4" fill="#be123c" />
                        <text x="50" y="53" font-size="7" font-weight="bold" fill="white" text-anchor="middle">ABA</text>
                    </svg>
                </div>

                <div class="text-[10px] text-slate-600 font-mono mt-2" id="txIdDisplay">
                    Ref: {{ $tranId ?? 'CB-PENDING' }}
                </div>
            </div>

            <!-- Payment Confirmation Form -->
            <form id="confirmPaymentForm" method="POST" action="{{ route('upgrade.confirm') }}">
                @csrf
                <input type="hidden" name="transaction_id" id="transactionIdInput" value="{{ $tranId ?? '' }}">
                
                <button type="submit" class="w-full py-3.5 px-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Confirm ABA Payment
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://checkout.payway.com.kh/plugins/checkout2-0.js"></script>
<script>
    function handleAbaCheckout() {
        if (typeof AbaPayway !== 'undefined') {
            try {
                AbaPayway.checkout();
            } catch (err) {
                openSimModal();
            }
        } else {
            openSimModal();
        }
    }

    function openSimModal() {
        document.getElementById('abaModal').classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
