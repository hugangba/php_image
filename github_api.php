<?php
class GitHubAPI {
    private $token;
    private $repo;
    private $owner;
    private $branch;
    private $api_url;
    private $custom_domain;

    public function __construct() {
        $config = include 'config.php';
        $this->token = $config['GITHUB_TOKEN'];
        $this->repo = $config['GITHUB_REPO'];
        $this->owner = $config['GITHUB_OWNER'];
        $this->branch = $config['GITHUB_BRANCH'];
        $this->custom_domain = rtrim($config['CUSTOM_DOMAIN'], '/');
        $this->api_url = "https://api.github.com/repos/{$this->owner}/{$this->repo}";
    }

    public function uploadFile($file_path, $file_name) {
        $content = base64_encode(file_get_contents($file_path));
        $url = "{$this->api_url}/contents/images/{$file_name}";

        $data = json_encode([
            'message' => "Upload {$file_name}",
            'content' => $content,
            'branch' => $this->branch
        ]);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-GitHub-API"
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = ['message' => 'cURL 错误: ' . curl_error($ch)];
        }
        curl_close($ch);

        $result = json_decode($response, true);
        if (isset($result['content']['name'])) {
            $result['direct_url'] = "{$this->custom_domain}/images/{$file_name}";
        }

        return $result;
    }

    public function getImages() {
        $url = "{$this->api_url}/contents/images?ref={$this->branch}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->token}",
            "Accept: application/vnd.github.v3+json",
            "User-Agent: PHP-GitHub-API"
        ]);

        $response = curl_exec($ch);
        if (curl_errno($ch)) {
            $response = ['message' => 'cURL 错误: ' . curl_error($ch)];
        }
        curl_close($ch);

        $images = json_decode($response, true);
        if (is_array($images)) {
            foreach ($images as &$image) {
                if (isset($image['name'])) {
                    $image['direct_url'] = "{$this->custom_domain}/images/{$image['name']}";
                }
            }
        }

        return $images;
    }
}
?>
