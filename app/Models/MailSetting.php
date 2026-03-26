<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailSetting  extends Model
{
      protected $table = 'smtp_details';

    protected $fillable = [
        'smtp_host',
        'smtp_port',
        'encryption',
        'smtp_username',
        'smtp_password',
        'from_email',
        'from_name'
    ];

 
}
