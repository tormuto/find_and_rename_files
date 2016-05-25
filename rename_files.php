<!DOCTYPE html>
<html>
	<head>
		<title>Recursively Find and Rename Files</title>
		<link rel='stylesheet' type='text/css' href='bootstrap.min.css'>
		<meta charset='utf-8'>
	</head>
	<body>
		<div class='container'>
		<?php

			$find=@$_POST['find'];
			$replace=@$_POST['replace'];
			$folder=@$_POST['folder'];
			$non_recursive=@$_POST['non_recursive'];
			$dont_overwrite=@$_POST['dont_overwrite'];
			$ignore_casing=@$_POST['ignore_casing'];
			
			if(!empty($_POST['start_replacing'])){
				if(empty($find))echo "<div class='alert alert-danger' >What do you want to replace.</div>";
				elseif(empty($replace))echo "<div class='alert alert-danger' >What is the replacement.</div>";
				elseif(empty($folder))echo "<div class='alert alert-danger' >Specify the folder.</div>";
				else {
					
					$path = realpath($folder);
					$rename_count=0;
					
					if($non_recursive){
						 $files = scandir($path);
						 foreach($files as $key=>$name){
							if(is_dir($name)||$name=='.'||$name=='..')continue;
							
							if($ignore_casing){
								if(stristr($name,$find)===false)continue;
								else $new_name=str_ireplace($find,$replace,$name);
							} else {
								if(strstr($name,$find)===false)continue;
								else $new_name=str_replace($find,$replace,$name);
							}
							
							if(file_exists($new_name)){
								if($dont_overwrite)continue;
								@unlink($new_name);  //remove directory recursive
							}

							if(empty($_POST['fake_rename']))rename("$path/$name","$path/$new_name");
							if(!empty($_POST['verbose']))echo "<div class='help-block'>rename('$path/$name',<br/>'$path/$new_name')</div>";
							$rename_count++;
						  }
					}
					else {
				
						$di = new RecursiveIteratorIterator(
							new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
							//RecursiveIteratorIterator::LEAVES_ONLY
							RecursiveIteratorIterator::CHILD_FIRST
						);

						foreach($di as $name => $fio) {
							$basic_filename=$fio->getFilename();
							$old_fullname = $fio->getPath() . DIRECTORY_SEPARATOR .$basic_filename;
							
							if($ignore_casing){
								if(stristr($basic_filename,$find)===false)continue;
								else $new_name=str_ireplace($find,$replace,$basic_filename);
							} else {
								if(strstr($basic_filename,$find)===false)continue;
								else $new_name=str_replace($find,$replace,$basic_filename);
							}
							
							$new_fullname=$fio->getPath() . DIRECTORY_SEPARATOR .$new_name;
							
							if(file_exists($new_fullname)){
								if($dont_overwrite)continue;
								@unlink($new_fullname);  //remove directory recursive
							}

							if(empty($_POST['fake_rename']))rename($old_fullname,$new_fullname);
							if(!empty($_POST['verbose']))echo "<div class='help-block'>rename('$old_fullname',<br/>$new_fullname)</div>";
							$rename_count++;
						}
					}
				
					echo "<div class='alert alert-success alert-sm'>$rename_count files renamed.</div>";
				}
			}
		?>
			<div class='col-sm-6 col-sm-offset-3'>
				<h3>Automatically rename files</h3>
				<div class='text-warning'>Make sure that the specified folder is NOT currently opened or beign used by the explorer or any other application</div>
				<hr/>
				<form method='post'>
					<div class='form-group' >
						<label for='replace'>Find Filename</label>
						<input type='text' name='find' placeholder='Find..' value='<?php echo $find; ?>' class='form-control input-sm' />
					</div>
					<div class='form-group' >
						<label for='replace'>Rename To</label>
						<input type='text' name='replace' placeholder='Replace..' value='<?php echo $replace; ?>'  class='form-control input-sm' />
					</div>
					<div class='form-group' >
						<label for='folder'>Folder</label>
						<input type='text' name='folder' id='folder' placeholder='C:\.....'  value='<?php echo $folder; ?>' class='form-control input-sm' />
					</div>
					<div class='checkbox' >
						<label>
							<input type='checkbox' name='non_recursive' value='1'  <?php if(!empty($non_recursive))echo 'checked'; ?>  > 
							Don't Replace in Sub-folders
						</label>
					</div>
					<div class='checkbox'>
						<label>
							<input type='checkbox' name='dont_overwrite' value='1'  <?php if(!empty($dont_overwrite))echo 'checked'; ?> > 
							Don't Overwrite if Name Exists
						</label>
					</div>
					<div class='checkbox'>
						<label>
							<input type='checkbox' name='ignore_casing' value='1'  <?php if(!empty($ignore_casing))echo 'checked'; ?> > 
							Ignore Casing
						</label>
					</div>
					<div class='checkbox'>
						<label>
							<input type='checkbox' name='verbose' value='1'  <?php if(!empty($_POST['verbose']))echo 'checked'; ?> > 
							Verbose 
						</label>
					</div>
					<div class='checkbox'>
						<label>
							<input type='checkbox' name='fake_rename' value='1'  <?php if(!empty($_POST['fake_rename']))echo 'checked'; ?> > 
							Fake Rename (Just simulate, but don't rename)
						</label>
					</div>
					<div class='text-center'>
						<button name='start_replacing' value='1' class='btn btn-sm btn-default' >Start Replacing</button>
					</div>
				</form>
			</div>
		</div>
	</body>
</html>