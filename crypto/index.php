<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crypto Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
        .ticker-scroll::-webkit-scrollbar {
            height: 8px;
        }
        .ticker-scroll::-webkit-scrollbar-thumb {
            background-color: #4a5568;
            border-radius: 4px;
        }
        .ticker-scroll::-webkit-scrollbar-track {
            background-color: #1a202c;
        }
    </style>
</head>

<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-500">

    <header class="bg-white dark:bg-gray-800 shadow-md py-4">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">Crypto Dashboard</h1>
            <nav class="flex items-center space-x-4">
                <a href="#" class="px-4 py-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200">Beranda</a>
                <a href="#" class="px-4 py-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200">Berita</a>
                <a href="#" class="px-4 py-2 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors duration-200">Kontak</a>
                <button id="theme-toggle" class="p-2 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors duration-200">
                    <svg class="w-6 h-6 text-gray-800 dark:text-gray-200" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
            </nav>
            <a href="ticker_crypto.php" class="flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Market Indodax
            </a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 text-center">Live Market Data</h2>
        
        <div x-data="marketTicker()" x-init="init()" class="mb-6 flex flex-nowrap gap-2 overflow-x-auto ticker-scroll">
            <template x-for="coin in coins" :key="coin">
                <div class="bg-white dark:bg-gray-800 p-2 rounded-xl shadow-md text-center flex-shrink-0 w-36 transition-all duration-300 hover:scale-105">
                    <span class="font-semibold text-sm" x-text="prices[coin] ? (prices[coin].symbol.toUpperCase() + '/IDR') : coin"></span>
                    <p x-show="prices[coin]" class="text-indigo-600 dark:text-indigo-400 font-bold text-base mt-1" x-text="prices[coin].idr.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })"></p>
                    <p x-show="!prices[coin]" class="text-xs text-gray-400">Memuat...</p>
                </div>
            </template>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-4 overflow-hidden" style="height: 600px;">
                <div class="tradingview-widget-container h-full w-full">
                    <div x-data="{ open: false, selectedSymbol: 'BINANCE:BTCUSDT', symbols: [ 'BINANCE:BTCUSDT', 'BINANCE:ETHUSDT', 'BINANCE:BNBUSDT', 'BINANCE:XRPUSDT', 'BINANCE:ADAUSDT' ], searchTerm: '' }" @click.outside="open = false" class="relative z-20 mb-4 w-1/2 lg:w-1/4">
                        <button @click="open = !open" class="w-full p-2 rounded-lg bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 flex justify-between items-center text-sm">
                            <span x-text="selectedSymbol.split(':')[1]"></span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" class="absolute w-full mt-2 bg-white dark:bg-gray-700 rounded-lg shadow-lg">
                            <input type="text" x-model="searchTerm" placeholder="Cari..." class="w-full p-2 bg-gray-50 dark:bg-gray-600 border-b dark:border-gray-500 rounded-t-lg focus:outline-none">
                            <ul class="max-h-60 overflow-y-auto">
                                <template x-for="symbol in symbols.filter(s => s.toLowerCase().includes(searchTerm.toLowerCase()))" :key="symbol">
                                    <li @click="selectedSymbol = symbol; open = false; updateTradingViewWidget(selectedSymbol);" class="p-2 hover:bg-indigo-100 dark:hover:bg-indigo-600 cursor-pointer text-sm">
                                        <span x-text="symbol.split(':')[1]"></span>
                                    </li>
                                </template>
                            </ul>
                        </div>
                    </div>
                    
                    <div id="tradingview_widget" class="h-full w-full"></div>
                    <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script>
                    <script type="text/javascript">
                        let tradingViewWidget = null;
                        function initTradingViewWidget(symbol) {
                            if (tradingViewWidget) {
                                tradingViewWidget.remove();
                            }
                            tradingViewWidget = new TradingView.widget({
                                "autosize": true,
                                "symbol": symbol,
                                "interval": "W",
                                "timezone": "Asia/Jakarta",
                                "theme": "dark",
                                "style": "1",
                                "locale": "id",
                                "toolbar_bg": "#1f2937",
                                "enable_publishing": false,
                                "withdateranges": true,
                                "hide_side_toolbar": false,
                                "allow_symbol_change": true,
                                "details": false,
                                "studies": [
                                    "PivotPointsHighLow@tv-basicstudies",
                                    "RSI@tv-basicstudies",
                                    "StochasticRSI@tv-basicstudies",
                                    "MACD@tv-basicstudies",
                                    "Volume@tv-basicstudies"
                                ],
                                "container_id": "tradingview_widget"
                            });
                        }
                        document.addEventListener('DOMContentLoaded', () => {
                            initTradingViewWidget('BINANCE:BTCUSDT');
                        });
                        function updateTradingViewWidget(symbol) {
                            initTradingViewWidget(symbol);
                        }
                    </script>
                </div>
            </div>

            <div class="lg:col-span-1 space-y-6">
                <div x-data="cryptoConverter()" x-init="init()" class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl transform transition-transform duration-300 hover:scale-[1.02]">
                    <h3 class="text-xl font-semibold mb-4 text-center">Konverter Harga Kripto Live 🇮🇩</h3>
                    <div class="flex items-center justify-center space-x-4 mb-4">
                        <template x-for="coin in coins" :key="coin.id">
                            <button @click="selectCoin(coin.id)" :class="{'bg-indigo-600 text-white': selectedCoin === coin.id, 'bg-gray-200 dark:bg-gray-700': selectedCoin !== coin.id}" class="py-2 px-4 rounded-lg font-medium transition-colors">
                                <span x-text="coin.symbol.toUpperCase()"></span>
                            </button>
                        </template>
                    </div>

                    <div class="text-center mb-4">
                        <span x-show="loading" class="text-sm text-gray-500">Memuat harga...</span>
                        <p x-show="!loading" class="text-4xl font-bold text-indigo-600 dark:text-indigo-400">
                            <span x-text="livePrice.toLocaleString('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 })"></span>
                        </p>
                    </div>

                    <p class="text-center text-sm text-gray-500 mb-4">Harga terkini dari CoinGecko</p>
                    
                    <div class="mb-4">
                        <label for="amount-input" class="block text-sm font-medium mb-1">Jumlah</label>
                        <input type="number" id="amount-input" x-model.number="amount" placeholder="Masukkan jumlah" class="w-full p-3 rounded-lg bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Hasil Konversi (IDR)</label>
                        <p x-text="(amount * livePrice).toLocaleString('id-ID', { style: 'currency', currency: 'IDR' })" class="w-full p-3 rounded-lg bg-indigo-100 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-200 font-bold text-lg text-center">
                            Rp 0
                        </p>
                    </div>
                </div>

                <div x-data="cryptoNews()" x-init="init()" class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-xl transform transition-transform duration-300 hover:scale-[1.02]">
                    <h3 class="text-xl font-semibold mb-4 text-center">Berita Terkini 📰</h3>
                    <ul class="space-y-4">
                        <template x-for="article in articles" :key="article.title">
                            <li class="border-b dark:border-gray-700 pb-2">
                                <a :href="article.url" target="_blank" class="block hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    <span x-text="article.title"></span>
                                    <p class="text-xs text-gray-500 mt-1" x-text="article.source.name"></p>
                                </a>
                            </li>
                        </template>
                        <p x-show="loading" class="text-center text-gray-500">Memuat berita...</p>
                        <p x-show="!loading && articles.length === 0" class="text-center text-gray-500">Tidak ada berita ditemukan.</p>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <footer class="text-center py-6 text-gray-500 dark:text-gray-400">
        <p>&copy; 2024 Crypto Dashboard. Ditenagai oleh TradingView, CoinGecko, dan NewsAPI.org</p>
    </footer>
    
    <script>
        // Skrip untuk Dark Mode
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        const themeToggleBtn = document.getElementById('theme-toggle');
        themeToggleBtn.addEventListener('click', () => {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            } else {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            }
        });

        // Skrip untuk Konverter Kripto
        function cryptoConverter() {
            return {
                amount: 1,
                livePrice: 0,
                loading: true,
                selectedCoin: 'bitcoin',
                coins: [
                    { id: 'bitcoin', symbol: 'btc' },
                    { id: 'ethereum', symbol: 'eth' }
                ],
                init() {
                    this.fetchPrices(this.selectedCoin);
                    setInterval(() => {
                        this.fetchPrices(this.selectedCoin);
                    }, 5000); 
                },
                fetchPrices(coinId) {
                    this.loading = true;
                    fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${coinId}&vs_currencies=idr`)
                        .then(response => response.json())
                        .then(data => {
                            if (data[coinId]) { 
                                this.livePrice = data[coinId].idr;
                            }
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching data:', error);
                            this.loading = false;
                        });
                },
                selectCoin(coinId) {
                    this.selectedCoin = coinId;
                    this.fetchPrices(this.selectedCoin);
                }
            }
        }

        // Skrip untuk Market Ticker
        function marketTicker() {
            return {
                prices: {},
                loading: true,
                coins: ['bitcoin', 'ethereum', 'tether', 'binancecoin', 'solana', 'litecoin', 'usd-coin', 'dogecoin', 'cardano', 'shiba-inu'],
                init() {
                    this.fetchTickerPrices();
                },
                fetchTickerPrices() {
                    this.loading = true;
                    const ids = this.coins.join(',');
                    fetch(`https://api.coingecko.com/api/v3/simple/price?ids=${ids}&vs_currencies=idr&include_24hr_change=true`)
                        .then(response => response.json())
                        .then(data => {
                            const formattedPrices = {};
                            for (const id in data) {
                                formattedPrices[id] = {
                                    idr: data[id].idr,
                                    symbol: id,
                                };
                            }
                            this.prices = formattedPrices;
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching ticker data:', error);
                            this.loading = false;
                        });
                }
            }
        }

        // Skrip untuk Berita Kripto
        function cryptoNews() {
            return {
                articles: [],
                loading: true,
                init() {
                    this.fetchNews();
                },
                fetchNews() {
                    this.loading = true;
                    const API_KEY = '662906a991e44ff7bcb0f9d511f2c0be';
                    const url = `https://newsapi.org/v2/everything?q=cryptocurrency&language=en&sortBy=publishedAt&apiKey=${API_KEY}`;
                    
                    fetch(url)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            this.articles = data.articles.slice(0, 5);
                            this.loading = false;
                        })
                        .catch(error => {
                            console.error('Error fetching news:', error);
                            this.loading = false;
                            this.articles = [];
                        });
                }
            }
        }
    </script>
</body>
</html>