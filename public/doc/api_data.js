define({ "api": [
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./doc/main.js",
    "group": "/Library/WebServer/Documents/laravel/laravel58/public/doc/main.js",
    "groupTitle": "/Library/WebServer/Documents/laravel/laravel58/public/doc/main.js",
    "name": ""
  },
  {
    "success": {
      "fields": {
        "Success 200": [
          {
            "group": "Success 200",
            "optional": false,
            "field": "varname1",
            "description": "<p>No type.</p>"
          },
          {
            "group": "Success 200",
            "type": "String",
            "optional": false,
            "field": "varname2",
            "description": "<p>With type.</p>"
          }
        ]
      }
    },
    "type": "",
    "url": "",
    "version": "0.0.0",
    "filename": "./docapi/main.js",
    "group": "/Library/WebServer/Documents/laravel/laravel58/public/docapi/main.js",
    "groupTitle": "/Library/WebServer/Documents/laravel/laravel58/public/docapi/main.js",
    "name": ""
  },
  {
    "type": "GET",
    "url": "http://192.168.248.128/engineers/{engineer_id}?token={token}",
    "title": "工程师信息查询 yuanbl1",
    "group": "engineers",
    "version": "2.0.0",
    "description": "<hr> <p>作者:yuanbl1</p> <p>创建时间:2017-07-21</p> <p>email:yuanbl1@qq.com</p> <p>备注:</p> <hr>",
    "parameter": {
      "fields": {
        "Parameter": [
          {
            "group": "Parameter",
            "type": "Number",
            "optional": false,
            "field": "engineer_id",
            "description": "<p>工程id</p>"
          }
        ]
      }
    },
    "success": {
      "fields": {
        "返回字段:": [
          {
            "group": "返回字段:",
            "type": "Number",
            "optional": false,
            "field": "status_code",
            "description": "<p>状态码</p>"
          },
          {
            "group": "返回字段:",
            "type": "String",
            "optional": false,
            "field": "message",
            "description": "<p>提示信息</p>"
          },
          {
            "group": "返回字段:",
            "type": "Json",
            "optional": false,
            "field": "data",
            "description": "<p>返回的数据包</p>"
          },
          {
            "group": "返回字段:",
            "type": "String",
            "optional": false,
            "field": "data.engineer_name",
            "description": "<p>工程师姓名</p>"
          },
          {
            "group": "返回字段:",
            "type": "String",
            "optional": false,
            "field": "data.name",
            "description": "<p>工程师别名</p>"
          },
          {
            "group": "返回字段:",
            "type": "String",
            "optional": false,
            "field": "data.str",
            "description": "<p>工程师描述</p>"
          }
        ]
      },
      "examples": [
        {
          "title": "成功1__时返回的数据:",
          "content": "HTTP/1.1 200 Success\n  {\n      \"status_code\": 200,\n      \"message\": \"OK\",\n      \"data\":{\n          \"engineer_name\": \"张三\",\n          \"engineer_code\": \"C16055\"\n      }\n}",
          "type": "json"
        },
        {
          "title": "成功2__时返回的数据:",
          "content": "HTTP/1.1 200 Success\n{\n      \"status_code\": 200,\n      \"message\": \"OK\",\n      \"data\":{\n          \"name\":\"hello\",\n          \"str\" :\"world\"\n      }\n}",
          "type": "json"
        }
      ]
    },
    "filename": "./docapi/demo.php",
    "groupTitle": "engineers",
    "name": "GetHttp192168248128EngineersEngineer_idTokenToken"
  }
] });
