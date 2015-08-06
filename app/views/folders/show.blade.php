@extends('master')

@section('content')
    @if(Session::has('folderMessage'))
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <p class="alert alert-success">
                {{ Session::get('folderMessage') }}
            </p>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col-md-6 col-xs-12">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <p class="panel-title">
                        Add Folder
                    </p>
                </div>
                @if($errors->has())
                    @foreach($errors->all() as $error)
                        <div class="alert alert-danger">{{ $error }}</div>
                    @endforeach
                @endif
                <div style="padding-top:30px" class="panel-body">
                    {{Form::open(array('url' => route('folders.store')))}}
                        <div class="row form-group">
                            <label for="parent_folder" class="col-md-3 control-label">Parent Folder</label>
                            {{ Form::select('parent', $parents, null, ['class' => 'col-md-8']) }}
                        </div>
                        <div class="row form-group">
                            <label for="folder_name" class="col-md-3 control-label">Folder Name:</label>
                            {{ Form::text('folder_name', null, ['class' => 'col-md-8']) }}
                        </div>
                        <p class="help-block">Please input your desired folder name to create inside the chosen parent folder.</p>
                        {{ Form::submit("Add", array('class' => 'btn btn-success btn-sm', 'type' => 'submit')) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
    <div class="row">    
        <div class="col-md-12 col-xs-12">
            <div class="panel panel-default">
            <!-- Default panel contents -->
                <div class="panel-heading">You're seeing folder: {{$folder_name}}</div>

                <!-- Table -->
                <!-- <div class="panel-body"> -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped table-hover" id="dataTables">
                            <thead>
                                <tr>
                                    <td>#</td>
                                    <td>File Name</td>
                                    <td>Type</td>
                                    <td>Extension</td>
                                    <td>Link</td>
                                    <td>File Size</td>
                                    <td colspan="2">Action</td>
                                    <td>Revisions</td>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $rownum=1;
                                ?>
                                @foreach($folders as $folder)
                                    <tr>
                                        <td>{{ $rownum++ }}</td>
                                        <td>
                                            <a href="{{ route('folders.destroy', array('folder_name' => substr($folder->folder_name,1).'/')) }}">
                                                {{ basename($folder->folder_name, $folder_name) }}
                                            </a>
                                        </td>
                                        <td>Folder</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>-</td>
                                        <td>
                                            {{ Form::open(array('method' => 'delete', 'url' => route('folders.destroy', array('folder_name' => $folder->key)),
                                                'onClick' => 'return confirm(\'Are you sure you want to delete '.$folder->folder_name.' and its files?\')' )) }}
                                                {{ Form::submit("Delete", array('class' => 'btn btn-danger btn-sm', 'type' => 'submit')) }}
                                            {{ Form::close() }}
                                        </td>
                                        <td>
                                            <a href="{{ route('folders.edit', array('key' => $folder->key)) }}">
                                                {{ Form::button('Edit', ['class' => 'btn btn-success btn-sm']) }}
                                            </a>
                                        </td>
                                        <td>-</td>
                                    </tr>
                                @endforeach
                                @foreach($files as $file)
                                <?php
                                    $targetdir = storage_path('files/'.Auth::user()->id.'/'.$file->folder_key.'/'.$file->key);
                                    $filename = $file->origFilename.'.'.$file->extension;
                                    $filesize = Helpers::getFileSize($targetdir.'/'.$filename)
                                ?>
                                    <tr>
                                    <td>{{ $rownum++ }}</td>
                                    <td>{{ $file->filename }}</td>
                                    <td>File</td>
                                    <td>{{ '.'.$file->extension }}</td>
                                    <td><a href="{{ route('files.show', array('key' => $file->key)) }}" >{{ route('files.show', array('key' => $file->key)) }}</a></td>
                                    <td>
                                        {{ $filesize }}
                                    </td>
                                    <td>
                                        {{ Form::open(array('method' => 'delete', 'url' => route('files.destroy', array('key' => $file->key)),
                                            'onClick' => 'return confirm(\'Are you sure you want to delete '.$file->filename.'.'.$file->extension.'?\')' )) }}
                                            {{ Form::submit("Delete", array('class' => 'btn btn-danger btn-sm', 'type' => 'submit')) }}
                                        {{ Form::close() }}
                                    </td>
                                    <td>
                                        <a href="{{ route('files.edit', array('key' => $file->key)) }}">
                                            {{ Form::button('Edit', ['class' => 'btn btn-success btn-sm'])}}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('revisions.show', array('key' => $file->key)) }}">
                                            {{ Form::button('Show', ['class' => 'btn btn-default btn-sm']) }}
                                        </a>
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
@stop