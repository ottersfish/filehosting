@extends('admin.master')

@section('content')
    @if(Session::has('message'))
    <div class="row">
        <div class="col-md-6 col-sm-8">
            <p class="alert alert-success">
                {{ @Session::get('message') }}
            </p>
        </div>
    </div>
    @endif

    <div class="row">
        {{ Form::model($user, array('method' => 'delete', 'url' => route('admin.users.destroy', array('id' => $user->id)))) }}
            <div class="row lead">
                <div class="col-md-12 col-xs-12">
                    Are you sure want to delete <strong>{{ $user->username }}?</strong>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                        <div class="panel-heading">List of {{ $user->username }} Files</div>

                        <!-- Table -->
                        <!-- <div class="panel-body"> -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>Folder Name</td>
                                            <td>File Name</td>
                                            <td>Extension</td>
                                            <td>Link</td>
                                            <td>File Size</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $rownum=1;
                                        if(isset($files->username))$usernameFlag=1;
                                        else $usernameFlag=0;
                                    ?>
                                        @foreach($files as $file)
                                        <?php
                                            $targetdir = storage_path('files/'.$file->id_user.'/'.$file->folder_key.'/'.$file->key);
                                            $filename = $file->origFilename.'.'.$file->extension;
                                            $filesize = Helpers::formatFileSize(filesize($targetdir.'/'.$filename));
                                        ?>
                                            <tr>
                                                <td>{{ $rownum++ }}</td>
                                                <td>{{ $file->folder_name }}</td>
                                                <td>{{ $file->filename }}</td>
                                                <td>{{ '.'.$file->extension }}</td>
                                                <td><a href="{{ route('files.show', array('key' => $file->key)) }}" >{{ route('files.show', array('key' => $file->key)) }}</a></td>
                                                <td>
                                                    {{ $filesize }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-xs-6">
                    <div class="panel panel-default">
                    <!-- Default panel contents -->
                        <div class="panel-heading">List of {{ $user->username }} Folders</div>

                        <!-- Table -->
                        <!-- <div class="panel-body"> -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-stripped table-hover" id="dataTables">
                                    <thead>
                                        <tr>
                                            <td>#</td>
                                            <td>Parent</td>
                                            <td>Folder Name</td>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        $rownum=1;
                                    ?>
                                        @foreach($folders as $folder)
                                            <tr>
                                                <td>{{ $rownum++ }}</td>
                                                <td>{{ $folder->parent }}</td>
                                                <td>{{ $folder->folder_name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        <!-- </div> -->
                    </div>
                </div>
            </div>
            {{ Form::submit("Delete", array("class"=>"btn btn-danger")) }}
        {{ Form::close() }}
    </div>
@stop