<?php echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>'; ?>
<html>
<head>
<title><?php echo $_REQUEST['n']; ?></title>
</head>
<body>
<embed src="svg/<?php echo $_REQUEST['f']; ?>" type="image/svg+xml" height="200" width="<?php echo $_REQUEST['w']; ?>" />
</body>
</html>