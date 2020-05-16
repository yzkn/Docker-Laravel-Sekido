<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use App\Music;
use App\User;

class ReceiveEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:receive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Receive  e-mails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        dump('handle');

        dump('handle');

        $imap_server = config('imap.host');
        $imap_port = config('imap.port');
        $imap_user = config('imap.username');
        $imap_pass = config('imap.password');
        // dump('$imap_server: ' . $imap_server);
        // dump('$imap_port: ' . $imap_port);
        // dump('$imap_user: ' . $imap_user);
        // dump('$imap_pass: ' . $imap_pass);
        $imap_senders = explode(',', config('imap.sender'));

        $this->imap_receive_emails($imap_server, $imap_port, $imap_user, $imap_pass, $imap_senders);
    }

    private function create_music($data)
    {
        dump('create_music(): ' . print_r($data, true));

        $path = [];

        if (isset($data['music']) && $data['music']) {
            $path['music'] = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('musics') . '/' . $data['music']);

            $getID3 = new \getID3();
            $tag = $getID3->analyze($path['music']);

            $music = new Music;

            $music->path = '/m/' . $data['music'];

            $FROM_ENC = 'ASCII,JIS,UTF-8,EUC-JP,SJIS';
            $music->album = mb_convert_encoding($tag['id3v2']['comments']['album'][0], 'UTF-8', $FROM_ENC) ?? '';
            $music->artist = mb_convert_encoding($tag['id3v2']['comments']['artist'][0], 'UTF-8', $FROM_ENC) ?? '';
            $music->bitrate = $tag['bitrate'] ?? '';
            $music->cover = '';
            $music->document = '';
            $music->genre = mb_convert_encoding($tag['id3v2']['comments']['genre'][0], 'UTF-8', $FROM_ENC) ?? '';
            $music->originalArtist = '';
            $music->playtime_seconds = $tag['playtime_seconds'] ?? '';
            $music->related_works = ($data['subject'] ?? '') . ' / ' . ($data['message'] ?? '');
            $music->title = mb_convert_encoding($tag['id3v2']['comments']['title'][0], 'UTF-8', $FROM_ENC) ?? '';
            $music->track_num = $tag['id3v2']['comments']['track_number'][0] ?? '';
            $music->year = $tag['id3v2']['comments']['recording_time'][0] ?? '';

            $user = User::where('email', $data['sender'])->first();
            if ($user) {
                // dump('$user: ' . print_r($user, true));
                $music->user_id = $user->id;
            } else {
                $music->user_id = 1;
            }
            dump('$music->user_id: ' . print_r($music->user_id, true));

            if (isset($data['document']) && $data['document']) {
                dump('$data[\'document\']: ' . print_r($data['document'], true));
                $music->document = '/d/' . $data['document'];

                $path['document'] = str_replace('/', DIRECTORY_SEPARATOR, Storage::path('documents') . '/' . $data['document']);
                dump('$path[\'document\']: ' . print_r($path['document'], true));
                $pdf_path = $path['document'];
                dump('read: ' . $pdf_path);
                $shell_cmd = 'magick -density 400 "' . $pdf_path . '" "' . $pdf_path . '.png"';
                dump('shell_cmd: ' . $shell_cmd);
                $output = shell_exec($shell_cmd);
                dump('output: ' . print_r($output, true));

                if (file_exists($pdf_path . '.png')) {
                    $music->cover = $music->document . '.png';
                }
            }

            $music->save();
        }
    }

    private function imap_receive_emails($imap_server, $imap_port, $imap_user, $imap_pass, $imap_senders)
    {
        $limit = 1;

        if (($mbox = imap_open('{' . $imap_server . ':' . $imap_port . '}INBOX', $imap_user, $imap_pass)) == false) {
            dump('Failed to connect to ' . $imap_server);
        } else {
            dump('connected to ' . $imap_server);

            dump('mbox: ' . print_r($mbox, true));
            $message_ids = imap_search($mbox, 'UNSEEN');
            dump('message_ids: ' . print_r($message_ids, true));
            foreach ($message_ids as $msgno) {
                $headerinfo = imap_headerinfo($mbox, $msgno);
                dump('imap_headerinfo ' . print_r($headerinfo, true));

                if (0 === count($imap_senders) || in_array($headerinfo->fromaddress, $imap_senders)) {
                    $subject = $this->getSubject($headerinfo);
                    dump($msgno . ':' . $subject);
                    $message_data = $this->getBody($mbox, $msgno);
                    $message_data['subject'] = $subject;
                    $message_data['sender'] = $headerinfo->fromaddress;
                    $this->create_music($message_data);
                }
            }

            imap_close($mbox);
            dump('imap_close');
        }
    }

    private function getSubject($header)
    {
        if (!isset($header->subject)) {
            return '';
        }
        $mhead = imap_mime_header_decode($header->subject);
        $subject = '';
        foreach ($mhead as $key => $value) {
            if ($value->charset == 'default') {
                $subject .= $value->text;
            } else {
                $subject .= mb_convert_encoding($value->text, 'UTF-8', $value->charset);
            }
        }
        return $subject;
    }

    private function getBody($mbox, $msgno)
    {
        dump('getBody($mbox, $msgno): ' . print_r($mbox, true), print_r($msgno, true));

        $return_data = [];

        $charset = null;
        $encoding = null;
        $attached_data = null;

        $info = imap_fetchstructure($mbox, $msgno);
        dump('$info');
        if (!empty($info->parts)) {
            dump('!empty($info->parts)');
            $parts_cnt = count($info->parts);
            for ($p = 0; $p < $parts_cnt; $p++) {
                dump('$info->parts[' . $p . ']: ' . print_r($info->parts[$p], true));

                if ($info->parts[$p]->type == 0) {
                    if (empty($charset)) {
                        $charset = $info->parts[$p]->parameters[0]->value;
                    }
                    if (empty($encoding)) {
                        $encoding = $info->parts[$p]->encoding;
                    }
                } elseif (!empty($info->parts[$p]->parts) && $info->parts[$p]->parts[$p]->type == 0) {
                    if (empty($charset)) {
                        $charset = $info->parts[$p]->parts[$p]->parameters[0]->value;
                    }
                    if (empty($encoding)) {
                        $encoding = $info->parts[$p]->parts[$p]->encoding;
                    }
                } elseif ($info->parts[$p]->type == 3 || $info->parts[$p]->type == 4) {
                    $files = imap_mime_header_decode($info->parts[$p]->parameters[0]->value);
                    if (!empty($files) && is_array($files)) {
                        $attached_data[$p]['file_name'] = null;
                        foreach ($files as $key => $file) {
                            if ($file->charset != 'default') {
                                $attached_data[$p]['file_name'] .= mb_convert_encoding($file->text, 'UTF-8', $file->charset);
                            } else {
                                $attached_data[$p]['file_name'] .= $file->text;
                            }
                        }
                    }
                    $attached_data[$p]['content_type'] = $info->parts[$p]->subtype;
                }
            }
        } else {
            dump('empty($info->parts)');
            $charset = $info->parameters[0]->value;
            $encoding = $info->encoding;
        }
        if (empty($charset)) {
            dump('empty($charset)');
        }

        $body = trim(imap_fetchbody($mbox, $msgno, 1, FT_INTERNAL));
        dump('trim($body)');

        if (!empty($body)) {
            dump('!empty($body)');

            switch ($encoding) {
                case 0:
                    $mail_body = mb_convert_encoding($body, "UTF-8", $charset);
                    break;
                case 1:
                    $encode_body = imap_8bit($body);
                    $encode_body = imap_qprint($encode_body);
                    $mail_body = mb_convert_encoding($encode_body, "UTF-8", $charset);
                    break;
                case 3:
                    $encode_body = imap_base64($body);
                    $mail_body = mb_convert_encoding($encode_body, "UTF-8", $charset);
                    break;
                case 4:
                    $encode_body = imap_qprint($body);
                    $mail_body = mb_convert_encoding($encode_body, 'UTF-8', $charset);
                    break;
                case 2:
                case 5:
                default:
                    dump('$encoding: ' . $encoding);
                    break;
            }

            dump('$mail_body');
            $return_data['message'] = $mail_body;
        } else {
            dump('empty($body)');
        }

        if (!empty($attached_data)) {
            dump('!empty($attached_data)');
            foreach ($attached_data as $key => $value) {
                $attached = imap_fetchbody($mbox, $msgno, $key + 1, FT_INTERNAL);
                if (empty($attached)) break;

                list($name, $ex) = explode('.', $value['file_name']);
                $file_name = md5($name . '_' . time() . '_' . $key . '.' . $ex) . '.' . $ex;
                $content_body = imap_base64($attached);
                $content_type = strtolower($value['content_type']);

                $parent = 'temp';
                if ('pdf' === $content_type) {
                    $parent = 'documents';
                    $return_data['document'] = $file_name;
                } else if ('mpeg' === $content_type) {
                    $parent = 'musics';
                    $return_data['music'] = $file_name;
                }

                $savePath = str_replace('/', DIRECTORY_SEPARATOR, Storage::path($parent) . '/' . $file_name);
                if ($fp = fopen($savePath, "w")) {
                    fwrite($fp, $content_body, strlen($content_body));
                    fclose($fp);
                    dump('Saved: ' . $file_name);
                }
            }
        }

        return $return_data;
    }
}
