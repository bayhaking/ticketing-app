<x-app-layout>
    <div style="padding: 2rem; text-align: center;">
        <h2 style="font-weight: 900; margin-bottom: 2rem; color: var(--accent);">SCANNER TIKET SPECTIX</h2>
        
        <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto; border-radius: 20px; overflow: hidden; border: 2px solid var(--border);"></div>
        
        <div id="result" style="margin-top: 2rem; padding: 2rem; border-radius: 1rem; display: none;">
            <h3 id="res-message" style="font-weight: 800;"></h3>
            <p id="res-detail" style="color: var(--text-sub);"></p>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        function onScanSuccess(decodedText, decodedResult) {
            // Berhenti scan sebentar biar ga dobel kirim
            html5QrcodeScanner.clear();
            
            fetch('{{ route("checkin.validate") }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_number: decodedText })
            })
            .then(res => res.json())
            .then(data => {
                const resDiv = document.getElementById('result');
                const resMsg = document.getElementById('res-message');
                
                resDiv.style.display = 'block';
                if(data.success) {
                    resDiv.style.background = 'rgba(29, 185, 84, 0.2)';
                    resMsg.style.color = '#1DB954';
                    resMsg.innerText = data.message;
                } else {
                    resDiv.style.background = 'rgba(255, 107, 107, 0.2)';
                    resMsg.style.color = '#ff6b6b';
                    resMsg.innerText = data.message;
                }
                
                // Restart scanner setelah 3 detik
                setTimeout(() => { 
                    resDiv.style.display = 'none';
                    location.reload(); 
                }, 3000);
            });
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
        html5QrcodeScanner.render(onScanSuccess);
    </script>
</x-app-layout>