<?php

namespace classes;

use Aws\Result;
use Aws\Ses\SesClient;
use classes\ini as _ini;
use Exception;

class email {

    const METHOD_SENDMAIL = 1;
    const METHOD_SES = 2;
    public static int $sending_method = self::METHOD_SENDMAIL;
    public $subject;
    public $content;
    public array $recipients = [
        ''    => [],
        'cc'  => [],
        'bcc' => [],
    ];
    public array $replacements = [];

    public static function set_statics() {
        static::$sending_method = _ini::get('email', 'method', self::METHOD_SENDMAIL);
    }

    public function set_recipients($base, $cc = [], $bcc = []) {
        $this->recipients[''] = $base;
        $this->recipients['cc'] = $cc;
        $this->recipients['bcc'] = $bcc;
    }

    public function set_subject($subject) {
        $this->subject = $subject;
    }

    public function set_content($content) {
        $this->content = $content;
    }

    public function load_template($file) {
        if (!file_exists($file)) {
            throw new Exception('Email template not found');
        } else {
            $this->content = file_get_contents($file);
        }
    }

    public function send(): Result|bool|null {
        $content = $this->do_replace($this->content);
        if (static::$sending_method == static::METHOD_SENDMAIL) {
            return mail(implode(',', $this->recipients['']), $this->do_replace($this->subject), $content);
        } else if (static::$sending_method == static::METHOD_SES) {
            require_once root . '/library/aws.phar';
            $controller = new SesClient([
                'version' => 'latest',
                'profile' => _ini::get('aws', 'profile'),
                'region'  => _ini::get('aws', 'region', 'eu-west-1'),
            ]);
            return $controller->sendEmail([
                'Source'      => _ini::get('email', 'from_address'),
                'Destination' => [
                    'ToAddresses'  => $this->recipients[''],
                    'CcAddresses'  => $this->recipients['cc'],
                    'BccAddresses' => $this->recipients['bcc'],
                ],
                'Message'     => [
                    'Subject' => [
                        'Data'    => $this->subject,
                        'Charset' => 'UTF8',
                    ],
                    'Body'    => [
                        'Text' => [
                            'Data'    => '',
                            'Charset' => 'UTF8',
                        ],
                        'Html' => [
                            'Data'    => $content,
                            'Charset' => 'UTF8',
                        ],
                    ],
                ],
            ]);
        }
        return null;
    }

    protected function do_replace($string): array|string {
        return str_replace(array_keys($this->replacements), array_values($this->replacements), $string);
    }
}
