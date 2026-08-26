@extends('layout.admin') {{-- <-- Ganti ini sesuai dengan @extends yang ada di file index.blade.php kamu --}}

@section('content')
<div class="container-fluid px-4 py-4" style="background-color: #f8f9fc; min-height: 100vh;">
    
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h4 class="mb-1 text-gray-800 fw-bold">⚙️ Management Panel: Edit Transaksi</h4>
                <p class="mb-0 text-muted small">Mengedit berkas tagihan untuk Invoice ID: <span class="badge bg-secondary">#{{ $transaction->id }}</span></p>
            </div>
            <a href="{{ route('admin.transactions.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7">
            
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-user text-primary me-2"></i>Data Pelanggan</h5>
                    <hr class="text-muted opacity-25 mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="small text-muted mb-1 d-block">Nama Lengkap</label>
                            <div class="p-3 bg-light rounded-3 fw-semibold text-dark">
                                {{ $transaction->customer_name ?? 'Tidak ada nama' }}
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label class="small text-muted mb-1 d-block">Alamat Email</label>
                            <div class="p-3 bg-light rounded-3 text-dark">
                                {{ $transaction->customer_email ?? '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-dark mb-0"><i class="fas fa-receipt text-success me-2"></i>Rincian Tagihan</h5>
                    <hr class="text-muted opacity-25 mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small text-muted mb-1 d-block">Order ID (Sistem / Midtrans)</label>
                            <div class="p-3 bg-light rounded-3 font-monospace text-secondary small">
                                {{ $transaction->order_id ?? $transaction->id }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="small text-muted mb-1 d-block">Total Nominal Pembayaran</label>
                            <div class="p-3 bg-success bg-opacity-10 text-success rounded-3 fw-bold h5 mb-0">
                                Rp {{ number_format($transaction->total_price ?? $transaction->gross_amount ?? 0, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-5">
            
            <div class="card border-0 shadow-sm rounded-3 mb-4 border-top border-primary border-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <h5 class="card-title fw-bold text-primary mb-0"><i class="fas fa-tasks me-2"></i>Status Operasional</h5>
                    <hr class="text-muted opacity-25 mt-3 mb-0">
                </div>
                <div class="card-body p-4">
                    
                    <div class="mb-4">
                        <label class="small text-muted mb-2 d-block">Status Saat Ini:</label>
                        @if(($transaction->status ?? '') == 'success')
                            <div class="alert alert-success border-0 rounded-3 py-2 px-3 mb-0 d-inline-block fw-semibold text-success">
                                <i class="fas fa-check-circle me-1"></i> Lunas / Success
                            </div>
                        @elseif(($transaction->status ?? '') == 'pending')
                            <div class="alert alert-warning border-0 rounded-3 py-2 px-3 mb-0 d-inline-block fw-semibold text-warning">
                                <i class="fas fa-clock me-1"></i> Pending Payment
                            </div>
                        @else
                            <div class="alert alert-danger border-0 rounded-3 py-2 px-3 mb-0 d-inline-block fw-semibold text-danger">
                                <i class="fas fa-times-circle me-1"></i> Gagal / Cancelled
                            </div>
                        @endif
                    </div>

                    <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="status" class="form-label small fw-bold text-dark">Ubah Status Menjadi:</label>
                            <select class="form-select form-select-lg rounded-3 @error('status') is-invalid @enderror" id="status" name="status" style="font-size: 0.95rem;" required>
                                <option value="pending" {{ ($transaction->status ?? '') == 'pending' ? 'selected' : '' }}>⏳ Pending (Menunggu Pembayaran)</option>
                                <option value="success" {{ ($transaction->status ?? '') == 'success' ? 'selected' : '' }}>✅ Success (Pembayaran Lunas)</option>
                                <option value="failed" {{ ($transaction->status ?? '') == 'failed' ? 'selected' : '' }}>❌ Failed / Cancel (Gagal)</option>
                            </select>
                            
                            @error('status')
                                <div class="invalid-feedback mt-2">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="p-3 bg-light rounded-3 text-muted mb-4" style="font-size: 0.8rem; border-left: 3px solid #f6c23e;">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i> 
                            Perubahan status di panel ini akan langsung memengaruhi akses tiket/layanan pelanggan. Pastikan dana sudah mutasi.
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100 rounded-3 shadow-sm py-2" style="font-size: 1rem;">
                            <i class="fas fa-save me-1"></i> Simpan Status Baru
                        </button>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection