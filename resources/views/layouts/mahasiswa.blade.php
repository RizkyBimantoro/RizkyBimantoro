@extends('layouts.app')

@section('content')
<h3>Data Mahasiswa</h3>
<button class="btn btn-primary mb-3" id="btn-tambah">Tambah Mahasiswa</button>

<table class="table table-bordered" id="table-mahasiswa">
    <thead>
        <tr>
            <th>NIM</th>
            <th>Nama</th>
            <th>Jenis Kelamin</th>
            <th>Tanggal lahir</th>
            <th>Jurusan</th>
            <th>Alamat</th>
            <th>Aksi</th>
        </tr>
    </thead>
</table>

<!-- Modal -->
<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="ModalAddLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="formMahasiswa" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalAddLabel">Modal title</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <div class="modal-body">
                <input type="hidden" id="edit_nim">

                <div class="mb-2">
                    <label for="">NIM</label>
                    <input type="text" class="form-control" id="nim" name="nim" placeholder="Masukan NIM">
                </div>

                <div class="mb-2">
                    <label for="">Nama</label>
                    <input type="text" class="form-control" id="nama" name="nama" placeholder="Masukan Nama">
                </div>

                <div class="mb-2">
                    <label for="">Jenis Kelamin</label>
                    <select name="jk" id="jk" class="form-control">
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="L">Laki-Laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="">Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tgl_lahir" name="tgl_lahir">
                </div>

                <div class="mb-2">
                    <label for="">Jurusan</label>
                    <select name="jurusan" id="jurusan" class="form-control">
                        <option value="">Pilih Jurusan</option>
                        <option value="Teknik Informatika">Teknik Informatika</option>
                        <option value="Sistem Informasi">Sistem Informasi</option>
                        <option value="Manajemen Informatika">Manajemen Informatika</option>
                        <option value="Ilmu Komunikasi">Ilmu Komunikasi</option>
                        <option value="Administrasi Publik">Administrasi Publik</option>
                        <option value="Teknik Mesin">Teknik Mesin</option>
                        <option value="Teknik Elektro">Teknik Elektro</option>
                        <option value="Teknik Sipil">Teknik Sipil</option>
                        <option value="Akuntansi">Akuntansi</option>
                        <option value="Manajemen">Manajemen</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label for="">Alamat</label>
                    <textarea name="alamat" id="alamat" class="form-control" rows="3" placeholder="Masukan Alamat"></textarea>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="btn-simpan">Save Data</button>
                <button type="button" class="btn btn-primary" id="btn-update">Edit Data</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var table;
    $(document).ready(function() {
        table = $('#table-mahasiswa').DataTable({
            ajax: {
                url: "/api/mahasiswa",
                dataSrc: function(json) {
                    // Jika response API dibungkus dalam object, ambil data array
                    console.log('DataTable response:', json);
                    return json.data || json; // opsi json jika data gaada
                }
            },
            columns: [
                { 
                    data: 'nim', 
                    name: 'nim' 
                },
                { 
                    data: 'nama', 
                    name: 'nama' 
                },
                { 
                    data: 'jk', 
                    name: 'jk' 
                },
                { 
                    data: 'tgl_lahir', 
                    name: 'tgl_lahir' 
                },
                { 
                    data: 'jurusan', 
                    name: 'jurusan' 
                },
                { 
                    data: 'alamat', 
                    name: 'alamat' 
                },
                {
                    data: 'nim',
                    render: function(nim) {
                        return `
                            <button class="btn btn-warning btn-sm btn-edit" data-id="${nim}">Edit</button>
                            <button class="btn btn-danger btn-sm btn-delete" data-id="${nim}">Hapus</button>
                        `;
                    }
                }
            ]
        });

        function ambildataForm(){
            return {
                nim: $('#nim').val(),
                nama: $('#nama').val(),
                jk: $('#jk').val(),
                tgl_lahir: $('#tgl_lahir').val(),
                jurusan: $('#jurusan').val(),
                alamat: $('#alamat').val()
            };
        }

        // Tambah Mahasiswa
        $('#btn-tambah').click(function() {
            $('#ModalAddLabel').text('Tambah Mahasiswa');
            $('#formMahasiswa')[0].reset();
            $('#nim').prop('readonly', false);
            $('#btn-simpan').show();
            $('#btn-update').hide();
            $('#ModalAdd').modal('show');
        });

        // Simpan Data Baru
        $('#btn-simpan').click(function() {
            var data = ambildataForm();
            $.ajax({
                url: '/api/mahasiswa',
                type: 'POST',
                data: data,
                success: function(response) {
                    $('#ModalAdd').modal('hide');
                    table.ajax.reload();
                    alert('Data berhasil disimpan');
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        // Edit Mahasiswa - DENGAN DEBUGGING
        $('#table-mahasiswa').on('click', '.btn-edit', function() {
            var nim = $(this).data('id');
            
            // DEBUG: Cek apakah NIM terambil
            console.log('NIM yang diklik:', nim);
            
            // Set nilai edit_nim untuk update nanti
            $('#edit_nim').val(nim);
            
            // DEBUG: Cek URL yang dipanggil
            var url = '/api/mahasiswa/' + nim;
            console.log('URL API:', url);
            
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json', // Pastikan response JSON
                beforeSend: function() {
                    console.log('Mengirim request ke:', url);
                },
                success: function(response) {
                    // DEBUG: Cek response yang diterima
                    console.log('Response diterima:', response);
                    
                    // Ambil data dari response.data
                    var data = response.data;
                    console.log('Data mahasiswa:', data);
                    
                    // Set modal title dan tampilkan modal
                    $('#ModalAddLabel').text('Edit Mahasiswa');
                    $('#ModalAdd').modal('show');
                    
                    // Reset form
                    $('#formMahasiswa')[0].reset();
                    
                    // return data
                    $('#nim').val(data.nim).prop('readonly', true);
                    $('#nama').val(data.nama);
                    $('#jk').val(data.jk);
                    $('#tgl_lahir').val(data.tgl_lahir);
                    $('#jurusan').val(data.jurusan);
                    $('#alamat').val(data.alamat);
                    
                    // DEBUG
                    // console.log('Form terisi:', {
                    //     nim: $('#nim').val(),
                    //     nama: $('#nama').val(),
                    //     jk: $('#jk').val(),
                    //     tgl_lahir: $('#tgl_lahir').val(),
                    //     jurusan: $('#jurusan').val()
                    // });
                    
                    // Atur tombol
                    $('#btn-simpan').hide();
                    $('#btn-update').show();
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:');
                    console.log('Status:', status);
                    console.log('Error:', error);
                    console.log('Response:', xhr.responseText);
                    console.log('Status Code:', xhr.status);
                    
                    alert('Error: ' + xhr.status + ' - ' + xhr.responseText);
                }
            });
        });

        // Update Data
        $('#btn-update').click(function () {
            var nim = $('#nim').val(); // nim bkn id
            var data = ambildataForm();   

            console.log('Update NIM:', nim);
            console.log('Update Data:', data);

            $.ajax({
                url: '/api/mahasiswa/' + nim,
                type: 'PUT',
                data: data,
                success: function (response) {
                    console.log('Update Success:', response);
                    $('#ModalAdd').modal('hide');
                    table.ajax.reload();
                    alert('Data berhasil diperbarui');
                },
                error: function (xhr) {
                    console.log('Update Error:', xhr.responseText);
                    alert('Error: ' + xhr.responseText);
                }
            });
        });

        // delete
        $('#table-mahasiswa').on('click', '.btn-delete', function() {
            var nim = $(this).data('id');
            
            if(confirm('Yakin ingin menghapus data mahasiswa dengan NIM: ' + nim + '?')) {
                $.ajax({
                    url: '/api/mahasiswa/' + nim,
                    type: 'DELETE',
                    success: function(response) {
                        table.ajax.reload();
                        alert('Data berhasil dihapus');
                    },
                    error: function(xhr) {
                        console.log('Error detail:', xhr.responseText);
                        alert('Error: ' + xhr.responseText);
                    }
                });
            }
        });
    });
</script>
@endsection