<?php

return [
    'oss' => [
        'sts' => [
            'AccessKeyID' => 'LTAI4FgiEMeBf6XkVJWqdi98',
            'AccessKeySecret' => 'WqoS98H0as2vwEnWAsgMuEEpt4YEQy',
            'roleArn' => 'acs:ram::1282075595203522:role/webupload',  // 角色资源描述符，在RAM的控制台的资源详情页上可以获取
            'regionId' => 'cn-shenzhen',
            'endpoint' => 'sts.cn-shenzhen.aliyuncs.com',
            'timeout' => '3600',  // 令牌过期时间
            'client_name' => 'client_name',  // setRoleSessionName可以不改
            // 在扮演角色(AssumeRole)时，可以附加一个授权策略，进一步限制角色的权限；
            // 详情请参考《RAM使用指南》
            // 这代表所有权限
            'policy' => [
                'Statement' => [
                    [
                        'Action' => ["oss:*"],
                        'Effect' => 'Allow',
                        'Resource' => ["acs:oss:*:*:*"],
                    ]
                ]
            ]
        ],
    ]
];
