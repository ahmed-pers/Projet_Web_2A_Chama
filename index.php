<?php
declare(strict_types=1);

// Send visitors to the FixMyCity sign-in page.
header('Location: FixMyCity/index.php?action=auth', true, 302);
exit;
