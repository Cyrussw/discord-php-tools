<?php
class Discord
{
    public $client_id, $client_secret, $redirect_uri, $scope;

    public function __construct($client_id, $client_secret, $redirect_uri, $scope)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->scope = $scope;
    }

    public function get_authorization($discord_code)
    {

        $payload = [
            "code" => $discord_code,
            "client_id" => $this->client_id,
            "client_secret" => $this->client_secret,
            "grant_type" => 'authorization_code',
            "redirect_uri" => $this->redirect_uri,
            "scope" => $this->scope,
        ];

        $payload_string = http_build_query($payload);
        $discord_token_url = "https://discordapp.com/api/oauth2/token";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $discord_token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = curl_exec($ch);

        if (!$result) {
            return curl_error($ch);
        }

        curl_close($ch);

        $result = json_decode($result, true);
        return $result;
    }

    public function user_details($access_token)
    {
        $ch = curl_init();
        $discord_users_url = "https://discordapp.com/api/users/@me";
        $header = ["Authorization: Bearer $access_token", "Content-Type: application/x-www-form-urlencoded"];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_URL, $discord_users_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // İstek sonucunu döndürmesi için CURLOPT_RETURNTRANSFER ayarlanır
        curl_setopt($ch, CURLOPT_POST, false);

        $result = curl_exec($ch);

        if (!$result) {
            // İstek başarısız olduğunda false döndürülür
            return false;
        }

        curl_close($ch);

        // JSON verisini diziye dönüştürerek döndürülür
        return json_decode($result, true);
    }

    public function get_user_guilds($access_token)
    {
        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://discordapp.com/api/users/@me/guilds',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    "Content-Type: application/x-www-form-urlencoded",
                    "Authorization: Bearer $access_token"
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function refresh_token($refresh_token)
    {
        $formdata1 = array(
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $refresh_token,
        );

        $encoded_data = http_build_query($formdata1);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://discord.com/api/v10/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encoded_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            'Content-Type: application/x-www-form-urlencoded',
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
?>