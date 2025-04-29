import time
import mysql.connector
import requests
from bs4 import BeautifulSoup

def get_db_connection(retries=10, delay=5):
    while retries > 0:
        try:
            db = mysql.connector.connect(
                host="localhost",
                user="botuser",
                password="botpass",
                database="otomoto_olx_bot",
                charset='utf8mb4',
                collation='utf8mb4_general_ci'
            )
            print("Connected to MySQL!")
            return db
        except mysql.connector.Error as e:
            print(f"MySQL connection failed ({e}), retrying in {delay} seconds...")
            retries -= 1
            time.sleep(delay)
    raise Exception("Could not connect to database after several retries.")

def get_filters():
    try:
        db = get_db_connection()
        cursor = db.cursor(dictionary=True)
        cursor.execute("SELECT * FROM filters")
        filters = cursor.fetchall()
        cursor.close()
        db.close()
        return filters
    except mysql.connector.Error as e:
        print(f"Database error: {e}")
        return []

def save_offer(filter_id, title, price, url, image_url=None):
    db = get_db_connection()
    cursor = db.cursor()
    cursor.execute("SELECT id FROM offers WHERE offer_url = %s", (url,))
    exists = cursor.fetchone()

    if not exists:
        cursor.execute(
            "INSERT INTO offers (filter_id, title, price, offer_url, image_url, created_at, updated_at) VALUES (%s, %s, %s, %s, %s, NOW(), NOW())",
            (filter_id, title, price, url, image_url)
        )
        db.commit()

    cursor.close()
    db.close()

def build_olx_url(category, search_text, price_from, price_to, year_from, year_to):
    base_url = "https://www.olx.pl/"
    
    # motoryzacja
    if category == 'ciezarowe':
        path = "motoryzacja/ciezarowe/"
    elif category == 'budowlane':
        path = "motoryzacja/budowlane/"
    elif category == 'osobowe':
        path = "motoryzacja/samochody/"
    elif category == 'dostawcze':
        path = "motoryzacja/dostawcze/"
    elif category == 'motocykle':
        path = "motoryzacja/motocykle-skutery/"
    elif category == 'przyczepy':
        path = "motoryzacja/przyczepy-i-naczepy/"
    # elektronika
    elif category == 'komputery':
        path = "elektronika/komputery/"
    elif category == 'telefony':
        path = "elektronika/telefony/"
    elif category == 'podzespoly':
        path = "elektronika/komputery/podzespoly-i-czesci/"
    else:
        path = ""
    
    if search_text:
        search_text = search_text.strip().replace(' ', '-')
        path += f"q-{search_text}/"

    params = []
    if price_from:
        params.append(f"search%5Bfilter_float_price:from%5D={price_from}")
    if price_to:
        params.append(f"search%5Bfilter_float_price:to%5D={price_to}")
    if year_from:
        params.append(f"search%5Bfilter_float_year:from%5D={year_from}")
    if year_to:
        params.append(f"search%5Bfilter_float_year:to%5D={year_to}")

    url = base_url + path
    if params:
        url += "?" + "&".join(params)
    return url

def build_otomoto_url(category, search_text, price_from, price_to, year_from, year_to):
    if category == 'ciezarowe':
        path = "ciezarowe/"
    elif category == 'budowlane':
        path = "maszyny-budowlane/sprzedaz/"
    elif category == 'osobowe':
        path = "osobowe/"
    elif category == 'dostawcze':
        path = "dostawcze/"
    elif category == 'motocykle':
        path = "motocykle-i-quady/"
    elif category == 'przyczepy':
        path = "przyczepy/"
    elif category == 'rolnicze':
        path = "maszyny-rolnicze/"
    else:
        path = ""

    base_url = f"https://www.otomoto.pl/{path}od-{year_from or 0}"
    if search_text:
        search_text = search_text.strip().replace(' ', '-')
        base_url += f"/q-{search_text}/"
    params = []
    if price_from:
        params.append(f"search%5Bfilter_float_price%3Afrom%5D={price_from}")
    if price_to:
        params.append(f"search%5Bfilter_float_price%3Ato%5D={price_to}")
    if year_to:
        params.append(f"search%5Bfilter_float_year%3Ato%5D={year_to}")
    params.append("search%5Badvanced_search_expanded%5D=true")

    return base_url + "?" + "&".join(params)

def fetch_olx(filter_id, url):
    print(f"Fetching OLX URL: {url}")
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
        "Accept-Language": "pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7"
    }
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')
    ads = soup.find_all('div', {'data-cy': 'l-card'})

    for ad in ads:
        title_tag = ad.find('h4', class_='css-1g61gc2')
        price_tag = ad.find('p', {'data-testid': 'ad-price'})
        link_tag = ad.find('a', class_='css-1tqlkj0')
        img_tag = ad.find('img')

        if title_tag and price_tag and link_tag:
            title = title_tag.text.strip()
            price = price_tag.text.strip()
            href = link_tag['href']
            
            if href.startswith('/'):
                ad_url = 'https://www.olx.pl' + href
            elif href.startswith('http'):
                ad_url = href
            else:
                ad_url = 'https://' + href.lstrip('/')

            img_url = img_tag.get('src') if img_tag else None

            save_offer(filter_id, title, price, ad_url, img_url)

def fetch_otomoto(filter_id, url):
    print(f"Fetching OTOMOTO URL: {url}")
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/123.0.0.0 Safari/537.36",
        "Accept-Language": "pl-PL,pl;q=0.9,en-US;q=0.8,en;q=0.7"
    }
    response = requests.get(url, headers=headers)
    soup = BeautifulSoup(response.text, 'html.parser')

    ads = soup.find_all('article')
    print(f"Found {len(ads)} articles on the page.")

    for ad in ads:
        title_tag = ad.find('h2', class_='e4b361b0')
        link_tag = title_tag.find('a') if title_tag else None
        price_container = ad.find('div', class_='ooa-rz87wg')
        price_tag = price_container.find('h3', class_='e1xre11z2') if price_container else None
        img_tag = ad.find('img')

        if link_tag and price_tag:
            title = link_tag.get_text(strip=True)
            ad_url = link_tag.get('href')
            price = price_tag.get_text(strip=True)
            img_url = img_tag['src'] if img_tag else None

            if not ad_url.startswith('http'):
                ad_url = 'https://www.otomoto.pl' + ad_url

            save_offer(filter_id, title, price, ad_url, img_url)

def main():
    filters = get_filters()
    for f in filters:
        if f['site'] == 'olx':
            url = build_olx_url(f['category'], f['search_text'], f['price_from'], f['price_to'], f['year_from'], f['year_to'])
            fetch_olx(f['id'], url)
        elif f['site'] == 'otomoto':
            url = build_otomoto_url(f['category'], f['search_text'], f['price_from'], f['price_to'], f['year_from'], f['year_to'])
            fetch_otomoto(f['id'], url)

if __name__ == "__main__":
    main()
