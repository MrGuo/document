import scrapy

class DmozSpider(scrapy.Spider):
    name = "dmoz"
    allowed_domains = ["dmoz.org"]
    start_urls = [
        "http://www.dmoz.org/Computers/Programming/Languages/Python/Books/",
        "http://www.dmoz.org/Computers/Programming/Languages/Python/Resources/"
    ]

    #def parse(self, response):
    #    filename = response.url.split("/")[-2]
    #    with open(filename, 'wb') as f:
    #        f.write(response.body)


    def parse(self, response):
        for sel in response.xpath('//div[@class="title-and-desc"]'):
            link = sel.xpath('a/@href').extract()
            title = sel.xpath('a/div[@class="site-title"]/text()').extract()
            desc = sel.xpath('div[@class="site-descr "]/text()').extract()
            print title, link, desc
