<?php

namespace classes;

use Aws\Result;
use Aws\Ses\SesClient;
use Exception;

class email
{

    const METHOD_SENDMAIL = 1;
    const METHOD_SES = 2;
    public static int $sending_method = self::METHOD_SES;
    public string $subject;
    public string $content;
    /** @var array<''|'cc'|'bcc', string[]> */
    public array $recipients = [
        ''    => [],
        'cc'  => [],
        'bcc' => [],
    ];
    /** @var string[] */
    public array $replacements = [];

    /**
     * @param string[] $base
     * @param string[] $cc
     * @param string[] $bcc
     */
    public function set_recipients(array $base, array $cc = [], array $bcc = []): void
    {
        $this->recipients[''] = $base;
        $this->recipients['cc'] = $cc;
        $this->recipients['bcc'] = $bcc;
    }

    public function set_subject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function set_content(string $content): void
    {
        $this->content = $content;
    }

    public function load_template(string $file): void
    {
        if (!file_exists($file)) {
            throw new Exception('Email template not found');
        } else {
            $this->content = file_get_contents($file);
        }
    }

    public function send(): Result|bool|null
    {
        $content = $this->do_replace($this->content);
        if (static::$sending_method == static::METHOD_SENDMAIL) {
            return mail(implode(',', $this->recipients['']), $this->do_replace($this->subject), $content);
        } else if (static::$sending_method == static::METHOD_SES) {
            try {
                $controller = new SesClient([
                    'version' => 'latest',
                    'profile' => getenv('awsProfile') ?: 'uknxcl',
                    'region'  => getenv('awsRegion') ?: 'eu-west-1',
                ]);
                return $controller->sendEmail([
                    'Source'      => getenv('emailFromAddress') ?: 'robchett@gmail.com',
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
            } catch (\Exception) {
            }
        }
        return null;
    }

    protected function do_replace(string $string): string
    {
        return str_replace(array_keys($this->replacements), array_values($this->replacements), $string);
    }
}
