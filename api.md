
## 商人（Merchant）

### POST api/merchants/auth/register

商人注册

* Request:
    ```
    {
        "alias": "test",
        "password": "test123",
        "money": 1234.5,
        "account": 12345678
    }
    ```

* Response:
    ```
    //成功返回success(201)，失败返回failed(400或其它)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### POST api/merchants/auth/login

商人登录

* Request:
    ```
    {
        "account": 12345678,
        "password": "test123"
    }
    ```

* Response:
    ```
    //成功时返回success(200)并附加Token
    {
        "status": "success",
        "merchant": {
            "id": 1,
            "alias": "test",
            "account": 12345678,
            "register_time": "2018-10-26 16:39:58",
            "money": 1234.5
        }
    }
    //失败时返回failed(400)并且不会附加Token
    {
        "status": "failed",
        "msg": "message"
    }
    ```

### GET api/merchants/{muid}(**需要验证**)

获取商人信息

* Response:
    ```
    //成功时(200)
    {
        "status": "success",
        "merchant": {
            "id": 1,
            "alias": "test",
            "account": 12345678,
            "register_time": "2018-10-26 16:39:58",
            "money": 1234.5
        }
    }
    //失败时(400)
    {
        "status": "failed",
        "msg": "message"
    }
    ```

### GET api/merchants/{muid}/logout(**需要验证**)

商人登出

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### PUT api/merchants/{muid}(**需要验证**)

更新商人信息

* Request:
    ```
    {
        "alias": "test",
        "money": 1234.5,
        "password": "test123"
    }
    ```

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### POST api/merchants/{muid}/commodities(**需要验证**)

商人添加货物

* Request:
    ```
    {
        "count": 10,
        "price": 100.5,
        "name": "test"
    }
    ```

* Response:
    ```
    //成功时返回success(201)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### GET api/merchants/{muid}/commodities(**需要验证**)

商人获取货物信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "commodities": [
            {
                "id": 1,
                "count": 10,
                "price": 100.5,
                "name": "test"
            },
            ...
        ]
    }
    ```

### PUT api/merchants/{muid}/commodities/{cid}(**需要验证**)

商人更新货物

* Request:
    ```
    {
        "count": 10,
        "price": 100.5,
        "name": "test"
    }
    ```

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### DELETE api/merchants/{muid}/commodities/{cid}(**需要验证**)

商人删除货物

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### GET api/merchants/{muid}/log_records(**需要验证**)

商人查看登录信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "log_records": [
            {
                "behaviour": "login",
                "time": "2018-10-25 13:49:06"
            },
            ...
        ]
    }
    ```

### GET api/merchants/{muid}/commodity_records(**需要验证**)

商人查看进货记录

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "commodity_records": [
            {
                "commodity_name": "test",
                "commodity_price": 100.5
                "number": 10,
                "time": "2018-10-25 13:49:06"
            },
            ...
        ]
    }
    ```

### GET api/merchants/{muid}/order_records(**需要验证**)

商人查看订单信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "order_records": [
            {
                "purchaser_alias": "test",
                "purchaser_account": 12345678,
                "commodity_name": "test",
                "commodity_price": 100.5,
                "number": 10,
                "time": "2018-10-25 13:49:06"
            },
            ...
        ]
    }
    ```

---

## 买家

### POST api/purchasers/auth/register

买家注册

* Request:
    ```
    {
        "alias": "test",
        "password": "test123",
        "blance": 1234.5,
        "account": 12345678
    }
    ```

* Response:
    ```
    //成功返回success(201)，失败返回failed(400或其它)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### POST api/purchasers/auth/login

买家登录

* Request:
    ```
    {
        "account": 12345678,
        "password": "test123"
    }
    ```

* Response:
    ```
    //成功时返回success(200)并附加Token
    {
        "status": "success",
        "purchaser": {
            "id": 1,
            "alias": "test",
            "account": 12345678,
            "register_time": "2018-10-26 16:39:58",
            "blance": 1234.5
        }
    }
    //失败时返回failed(400)并且不会附加Token
    {
        "status": "failed",
        "msg": "message"
    }
    ```

### GET api/purchasers/{puid}(**需要验证**)

获取买家信息

* Response:
    ```
    {
        "status": "success/falied",
        "purchaser": {
            "id": 1,
            "alias": "test",
            "account": 12345678,
            "register_time": "2018-10-26 16:39:58",
            "blance": 1234.5
        }
    }
    ```

### GET api/purchasers/{puid}/logout(**需要验证**)

买家登出

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### PUT api/purchasers/{puid}(**需要验证**)

更新买家信息

* Request:
    ```
    {
        "alias": "test",
        "blance": 1234.5,
        "password": "test123"
    }
    ```

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### POST api/purchasers/{puid}/order_records(**需要验证**)

买家购买商品

* Request:
    ```
    {
        "commodity_id": 1,
        "count": 10
    }
    ```

* Response:
    ```
    //成功时返回success(201)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "msg": "message"
    }
    ```

### GET api/purchasers/{puid}/log_records(**需要验证**)

买家查看登录信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "log_records": [
            {
                "behaviour": "login",
                "time": "2018-10-25 13:49:06"
            },
            ...
        ]
    }
    ```

### GET api/purchasers/{puid}/order_records(**需要验证**)

买家查看订单信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "order_records": [
            {
                "merchant_alias": "test",
                "merchant_account": 12345678,
                "commodity_name": "test",
                "commodity_price": 100.5,
                "number": 10,
                "time": "2018-10-25 13:49:06"
            },
            ...
        ]
    }
    ```

### GET api/commodities

获取商品信息

* Response:
    ```
    //成功时返回success(200)，失败时返回failed(400/404)
    {
        "status": "success/failed",
        "commodities": [
            {
                "id": 1,
                "count": 10,
                "price": 100.5,
                "name": "test"
            },
            ...
        ]
    }
    ```
