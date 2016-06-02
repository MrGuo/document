从第三方抓取图片，存入我们本地文件，但最近发现同步过来的图片处理失败。

通过抓取header发现

HTTP/1.1 200 OK
Server: Tengine/2.1.1
Date: Thu, 02 Jun 2016 05:13:04 GMT
Content-Type: image/jpeg
Connection: keep-alive
Expires: Thu, 02 Jun 2016 05:13:04 GMT
Last-Modified: Tue, 31 May 2016 05:03:39 GMT
Cache-Control: max-age=86400
Content-Encoding: gzip
X-Ser: xxx
X-Cache: xxx


下面是正常图片的header

HTTP/1.1 200 OK
Server: Apache/2.0.58
Date: Thu, 02 Jun 2016 05:13:32 GMT
Content-Type: image/jpeg
Content-Length: 68030
Last-Modified: Wed, 01 Jun 2016 04:08:15 GMT
Connection: keep-alive
ETag: "xxx"
Expires: Fri, 03 Jun 2016 05:13:32 GMT
Cache-Control: max-age=86400
Accept-Ranges: bytes

是Content-Encoding压缩引起的问题。

google 一个解决方案

http://xcgx.me/archives/351

```
def get_image(url,image_path):
    print("get_image:",url)
    try:
        g = gzip.GzipFile(mode="rb",fileobj=urllib.request.urlopen(url))
        image_data=g.read()
    except:
        image_data=urllib.request.urlopen(url).read()
 
    filename = url[url.rindex('/') + 1:]
    #print(filename)
    img_f = open(image_path + filename, "wb")
    img_f.write(image_data)
    img_f.close()
（写法比较奇葩……）

```
