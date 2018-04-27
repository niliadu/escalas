<?php

session_start();
unset($_SESSION);
session_destroy();
header('location: http://servicos.decea.intraer/lpna/gerencial');

