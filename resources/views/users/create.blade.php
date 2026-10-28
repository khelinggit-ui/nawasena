@extends('layouts.master')
@section('title', 'Create User')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
             <div class="page-title-box mt-3">
                 <h4 class="mb-sm-0">Create User</h4>
             </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-12 col-md-10 col-lg-8">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Jabatan</label>
                            <select name="jabatan_id" class="form-control" required>
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($jabatans as $j)
                                    <option value="{{ $j->id }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Cabang</label>
                            <select name="cabang_id" class="form-control" required>
                                <option value="">-- Pilih Cabang --</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}">{{ $c->nama_cabang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Kantor</label>
                            <select name="kantor_id" class="form-control">
                                <option value="">-- Pilih Kantor --</option>
                                @foreach($kantors as $kantor)
                                    <option value="{{ $kantor->id }}" {{ old('kantor_id') == $kantor->id ? 'selected' : '' }}>{{ $kantor->nama_kantor }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="spv_container" style="display: none;">
                            <label>SPV (Supervisor)</label>
                            <select name="spv_id" id="spv_id" class="form-control">
                                <option value="">-- Pilih SPV --</option>
                            </select>
                            <small class="text-muted">Muncul karena jabatan Marketing. Pilih SPV untuk meregistrasikan atasan.</small>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="Active" selected>Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Roles</label>
                            <select name="roles[]" class="form-control" multiple required>
                                @foreach($roles as $role)
                                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Save</button>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const allSpvs = @json($spvs);
        const jabatans = @json($jabatans);
        const currentUserSpvId = "{{ old('spv_id') }}";
        
        const jabatanSelect = document.querySelector('select[name="jabatan_id"]');
        const cabangSelect = document.querySelector('select[name="cabang_id"]');
        const spvContainer = document.getElementById('spv_container');
        const spvSelect = document.getElementById('spv_id');

        function updateSpvDropdown() {
            const jabatanId = jabatanSelect.value;
            const cabang = cabangSelect.value;
            
            let isMarketing = false;
            if (jabatanId) {
                const selectedJabatan = jabatans.find(j => j.id == jabatanId);
                if (selectedJabatan && selectedJabatan.nama_jabatan.toLowerCase().includes('marketing')) {
                    isMarketing = true;
                }
            }

            if (isMarketing) {
                spvContainer.style.display = 'block';
                // Filter SPVs by cabang
                let filteredSpvs = allSpvs;
                if (cabang) {
                    filteredSpvs = allSpvs.filter(spv => spv.cabang_id == cabang || spv.cabang_id == 8);
                }
                
                // Rebuild options
                spvSelect.innerHTML = '<option value="">-- Pilih SPV --</option>';
                filteredSpvs.forEach(spv => {
                    const selected = (spv.id == currentUserSpvId) ? 'selected' : '';
                    spvSelect.innerHTML += `<option value="${spv.id}" ${selected}>${spv.name}</option>`;
                });
            } else {
                spvContainer.style.display = 'none';
                spvSelect.innerHTML = '<option value="">-- Pilih SPV --</option>';
            }
        }

        jabatanSelect.addEventListener('change', updateSpvDropdown);
        cabangSelect.addEventListener('change', updateSpvDropdown);
        
        updateSpvDropdown();
    });
</script>
@endsection
