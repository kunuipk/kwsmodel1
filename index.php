<!doctype html>
<html lang="th" class="h-full">
 <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AI Pose Detection - khunhanwittayasan</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@1.3.1/dist/tf.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@teachablemachine/pose@0.8/dist/teachablemachine-pose.min.js"></script>
  <script src="/_sdk/element_sdk.js"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&amp;family=Orbitron:wght@400;500;600;700&amp;display=swap" rel="stylesheet">
  <style>
        body {
            box-sizing: border-box;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .neon-glow {
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.3), 
                        0 0 40px rgba(139, 92, 246, 0.2),
                        0 0 60px rgba(139, 92, 246, 0.1);
        }
        
        .neon-text {
            text-shadow: 0 0 10px rgba(139, 92, 246, 0.8),
                         0 0 20px rgba(139, 92, 246, 0.6),
                         0 0 30px rgba(139, 92, 246, 0.4);
        }
        
        .pulse-ring {
            animation: pulse-ring 2s cubic-bezier(0.455, 0.03, 0.515, 0.955) infinite;
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.7; }
            100% { transform: scale(0.95); opacity: 1; }
        }
        
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .scan-line {
            position: absolute;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #8b5cf6, transparent);
            animation: scan 2s linear infinite;
        }
        
        @keyframes scan {
            0% { top: 0; opacity: 0; }
            50% { opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        
        .prediction-bar {
            transition: width 0.3s ease-out;
        }
        
        .btn-glow:hover {
            box-shadow: 0 0 25px rgba(34, 197, 94, 0.5),
                        0 0 50px rgba(34, 197, 94, 0.3);
        }
        
        .btn-stop:hover {
            box-shadow: 0 0 25px rgba(239, 68, 68, 0.5),
                        0 0 50px rgba(239, 68, 68, 0.3);
        }
        
        .timer-display {
            font-family: 'Orbitron', monospace;
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(139, 92, 246, 0.6);
            border-radius: 50%;
            animation: particle-float 4s ease-in-out infinite;
        }
        
        @keyframes particle-float {
            0%, 100% { transform: translateY(0) translateX(0); opacity: 0.6; }
            25% { transform: translateY(-20px) translateX(10px); opacity: 1; }
            50% { transform: translateY(-10px) translateX(-10px); opacity: 0.8; }
            75% { transform: translateY(-30px) translateX(5px); opacity: 0.4; }
        }
        
        #canvas {
            border-radius: 16px;
        }
    </style>
  <style>@view-transition { navigation: auto; }</style>
  <script src="/_sdk/data_sdk.js" type="text/javascript"></script>
 </head>
 <body class="h-full gradient-bg text-white font-['Kanit'] overflow-auto">
  <div class="min-h-full w-full flex flex-col relative"><!-- Floating Particles Background -->
   <div class="absolute inset-0 overflow-hidden pointer-events-none">
    <div class="particle" style="left: 10%; top: 20%; animation-delay: 0s;"></div>
    <div class="particle" style="left: 20%; top: 60%; animation-delay: 0.5s;"></div>
    <div class="particle" style="left: 70%; top: 30%; animation-delay: 1s;"></div>
    <div class="particle" style="left: 80%; top: 70%; animation-delay: 1.5s;"></div>
    <div class="particle" style="left: 50%; top: 10%; animation-delay: 2s;"></div>
    <div class="particle" style="left: 30%; top: 80%; animation-delay: 2.5s;"></div>
    <div class="particle" style="left: 90%; top: 50%; animation-delay: 3s;"></div>
   </div><!-- Header -->
   <header class="py-6 px-4 text-center relative z-10">
    <div class="float-animation inline-block"><img src="https://waiwai-it.com/images/waiaijaidee-logo2025.png" alt="WAI AI JAI DEE Logo" loading="lazy" class="mx-auto h-auto max-w-[140px] drop-shadow-2xl" onerror="this.src=''; this.alt='Logo'; this.style.display='none';">
    </div>
    <h1 id="system-name" class="mt-4 text-2xl md:text-3xl font-bold bg-gradient-to-r from-purple-400 via-pink-400 to-cyan-400 bg-clip-text text-transparent">khunhanwittayasan</h1>
    <p class="mt-2 text-gray-400 text-sm">AI Pose Detection System</p>
   </header><!-- Main Content -->
   <main class="flex-1 px-4 pb-6 relative z-10">
    <div class="max-w-4xl mx-auto"><!-- Timer Section -->
     <div class="glass-card rounded-2xl p-4 mb-6 neon-glow">
      <div class="flex items-center justify-center gap-4 flex-wrap">
       <div class="flex items-center gap-2">
        <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg><span class="text-gray-300">เวลา���ำงา���:</span>
       </div>
       <div id="timer" class="timer-display text-3xl md:text-4xl font-bold neon-text">
        00:00:00
       </div>
      </div>
     </div><!-- Control Buttons -->
     <div class="flex justify-center gap-4 mb-6 flex-wrap"><button id="start-btn" onclick="startDetection()" class="btn-glow px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 flex items-center gap-2 shadow-lg">
       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
       </svg> เริ่มต้น </button> <button id="stop-btn" onclick="stopDetection()" disabled class="btn-stop px-8 py-3 bg-gradient-to-r from-red-500 to-rose-600 rounded-full font-semibold text-lg transition-all duration-300 transform hover:scale-105 flex items-center gap-2 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:scale-100">
       <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path> <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"></path>
       </svg> หยุด </button>
     </div><!-- Camera & Results Grid -->
     <div class="grid md:grid-cols-2 gap-6"><!-- Camera View -->
      <div class="glass-card rounded-2xl p-4 neon-glow">
       <h3 class="text-lg font-semibold mb-4 flex items-center gap-2 text-purple-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
        </svg> กล้อง Webcam</h3>
       <div class="relative aspect-square bg-gray-900/50 rounded-xl overflow-hidden flex items-center justify-center">
        <div id="camera-placeholder" class="text-center p-6">
         <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-purple-500/20 flex items-center justify-center pulse-ring">
          <svg class="w-10 h-10 text-purple-400" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
          </svg>
         </div>
         <p class="text-gray-400">กดปุ่ม "เริ่มต้น" เพื่อ���ปิดกล้อง</p>
        </div>
        <canvas id="canvas" class="hidden max-w-full max-h-full"></canvas>
        <div id="scan-line" class="scan-line hidden"></div>
       </div>
       <div id="status" class="mt-4 text-center"><span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-700/50 text-gray-300 text-sm"> <span class="w-2 h-2 rounded-full bg-gray-400"></span> รอเริ่มต้น </span>
       </div>
      </div><!-- Results Panel -->
      <div class="glass-card rounded-2xl p-6 neon-glow card-hover">
       <h3 class="text-xl font-bold mb-4 flex items-center gap-3 text-transparent bg-gradient-to-r from-cyan-400 to-blue-400 bg-clip-text">
        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center">
         <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
         </svg>
        </div> ผลการวิเคราะห์</h3>
       <div id="label-container" class="space-y-3 min-h-[200px]">
        <div class="text-center py-8 text-gray-400">
         <svg class="w-16 h-16 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
         </svg>
         <p class="font-medium">รอข้อมูลจากการวิเคราะห์</p>
         <p class="text-sm text-gray-500 mt-1">เริ่มระบบเพื่อดูผลลัพธ์</p>
        </div>
       </div><!-- Top Prediction Display -->
       <div id="top-prediction" class="mt-6 p-5 rounded-2xl bg-gradient-to-r from-purple-500/20 to-cyan-500/20 border-2 border-purple-500/30 hidden shimmer">
        <div class="text-center relative z-10">
         <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-500/20 border border-purple-500/30 mb-2"><span class="text-xs text-purple-300 font-medium">ท่าทางที่ตรวจพบ</span>
         </div>
         <p id="top-class" class="text-3xl font-bold bg-gradient-to-r from-purple-400 to-cyan-400 bg-clip-text text-transparent mb-2">-</p>
         <div class="flex items-center justify-center gap-2">
          <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p id="top-confidence" class="text-2xl font-bold text-green-400">0%</p>
         </div>
        </div>
       </div>
      </div>
     </div><!-- Instructions -->
     <div class="glass-card rounded-2xl p-6 mt-6 card-hover">
      <h3 class="text-xl font-bold mb-4 flex items-center gap-3 text-transparent bg-gradient-to-r from-yellow-400 to-orange-400 bg-clip-text">
       <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-yellow-500 to-orange-500 flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewbox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
       </div> วิธีใช้งาน</h3>
      <ul class="space-y-3 text-gray-300">
       <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-300"><span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-sm">1</span> <span>กดปุ่ม "เริ่มต้น" เพื่อเปิดกล้องและเริ่มการวิเคราะห์</span></li>
       <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-300"><span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center text-white font-bold text-sm">2</span> <span>ยืนให้เห็นทั้งตัวในกล้อง ระบบจะตรวจจับท่าทางอัตโนมัติ</span></li>
       <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-300"><span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-green-500 to-emerald-500 flex items-center justify-center text-white font-bold text-sm">3</span> <span>ผลการวิเคราะห์จะแสดงแบบเรียลไทม์พร้อมเปอร์เซ็นต์ความแม่นยำ</span></li>
       <li class="flex items-start gap-3 p-3 rounded-xl bg-gray-800/30 hover:bg-gray-800/50 transition-all duration-300"><span class="flex-shrink-0 w-7 h-7 rounded-lg bg-gradient-to-br from-red-500 to-rose-500 flex items-center justify-center text-white font-bold text-sm">4</span> <span>กดปุ่ม "หยุด" เมื่อต้องการหยุดการวิเคราะห์</span></li>
      </ul>
     </div>
    </div>
   </main><!-- Footer -->
   <footer class="py-4 px-4 text-center relative z-10">
    <p id="footer-text" class="text-gray-500 text-sm">© 2026 Dev by kws</p>
   </footer>
  </div>
  <script>
        // Configuration
        const defaultConfig = {
            system_name: 'khunhanwittayasan',
            footer_text: '© 2026 Dev by kws'
        };
        
        let config = { ...defaultConfig };
        
        // Teachable Machine Setup
        const URL = "./my_model/";
        let model, webcam, ctx, labelContainer, maxPredictions;
        let isRunning = false;
        let timerInterval = null;
        let seconds = 0;
        let animationFrameId = null;
        
        // Color palette for predictions
        const predictionColors = [
            { from: 'from-purple-500', to: 'to-purple-600', bar: 'bg-purple-500' },
            { from: 'from-cyan-500', to: 'to-cyan-600', bar: 'bg-cyan-500' },
            { from: 'from-pink-500', to: 'to-pink-600', bar: 'bg-pink-500' },
            { from: 'from-green-500', to: 'to-green-600', bar: 'bg-green-500' },
            { from: 'from-yellow-500', to: 'to-yellow-600', bar: 'bg-yellow-500' },
            { from: 'from-red-500', to: 'to-red-600', bar: 'bg-red-500' },
            { from: 'from-indigo-500', to: 'to-indigo-600', bar: 'bg-indigo-500' },
            { from: 'from-orange-500', to: 'to-orange-600', bar: 'bg-orange-500' }
        ];
        
        // Timer functions
        function updateTimer() {
            seconds++;
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;
            document.getElementById('timer').textContent = 
                `${hrs.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        }
        
        function startTimer() {
            seconds = 0;
            document.getElementById('timer').textContent = '00:00:00';
            timerInterval = setInterval(updateTimer, 1000);
        }
        
        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }
        
        // Update status display
        function updateStatus(text, color) {
            const statusEl = document.getElementById('status');
            const colorClasses = {
                'green': 'bg-green-400',
                'red': 'bg-red-400',
                'yellow': 'bg-yellow-400',
                'gray': 'bg-gray-400'
            };
            statusEl.innerHTML = `
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-gray-700/50 text-gray-300 text-sm">
                    <span class="w-2 h-2 rounded-full ${colorClasses[color] || colorClasses.gray} ${color === 'green' ? 'animate-pulse' : ''}"></span>
                    ${text}
                </span>
            `;
        }
        
        // Start detection
        async function startDetection() {
            if (isRunning) return;
            
            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');
            
            startBtn.disabled = true;
            updateStatus('กำลังโหลดโมเดล...', 'yellow');
            
            try {
                const modelURL = URL + "model.json";
                const metadataURL = URL + "metadata.json";
                
                // Load model
                model = await tmPose.load(modelURL, metadataURL);
                maxPredictions = model.getTotalClasses();
                
                // Setup webcam
                const size = 300;
                const flip = true;
                webcam = new tmPose.Webcam(size, size, flip);
                await webcam.setup();
                await webcam.play();
                
                // Setup canvas
                const canvas = document.getElementById("canvas");
                canvas.width = size;
                canvas.height = size;
                ctx = canvas.getContext("2d");
                
                // Show canvas, hide placeholder
                document.getElementById('camera-placeholder').classList.add('hidden');
                canvas.classList.remove('hidden');
                document.getElementById('scan-line').classList.remove('hidden');
                
                // Setup label container
                labelContainer = document.getElementById("label-container");
                labelContainer.innerHTML = '';
                
                for (let i = 0; i < maxPredictions; i++) {
                    const colorSet = predictionColors[i % predictionColors.length];
                    const div = document.createElement("div");
                    div.className = 'prediction-item';
                    div.innerHTML = `
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-sm font-medium text-gray-300">Class ${i + 1}</span>
                            <span class="text-sm font-bold text-white prediction-value">0%</span>
                        </div>
                        <div class="h-3 bg-gray-700/50 rounded-full overflow-hidden">
                            <div class="prediction-bar h-full ${colorSet.bar} rounded-full transition-all duration-300" style="width: 0%"></div>
                        </div>
                    `;
                    labelContainer.appendChild(div);
                }
                
                // Show top prediction panel
                document.getElementById('top-prediction').classList.remove('hidden');
                
                isRunning = true;
                startBtn.disabled = true;
                stopBtn.disabled = false;
                
                startTimer();
                updateStatus('กำลังวิเค��าะห์...', 'green');
                
                loop();
                
            } catch (error) {
                console.error('Error starting detection:', error);
                updateStatus('เกิดข้อผิดพลาด: ' + error.message, 'red');
                startBtn.disabled = false;
                
                // Show error in label container
                labelContainer.innerHTML = `
                    <div class="text-center py-8 text-red-400">
                        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="font-medium">ไม่สามารถโหลดโมเ��ลได้</p>
                        <p class="text-sm mt-2 text-gray-400">กรุณาตรวจสอบว่าไฟล์โมเดลอยู่ในโฟลเดอร์ ./my_model/</p>
                    </div>
                `;
            }
        }
        
        // Stop detection
        function stopDetection() {
            if (!isRunning) return;
            
            isRunning = false;
            
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                animationFrameId = null;
            }
            
            if (webcam) {
                webcam.stop();
            }
            
            stopTimer();
            
            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');
            
            startBtn.disabled = false;
            stopBtn.disabled = true;
            
            // Hide scan line
            document.getElementById('scan-line').classList.add('hidden');
            
            updateStatus('หยุดการทำงาน', 'red');
        }
        
        // Main loop
        async function loop() {
            if (!isRunning) return;
            
            webcam.update();
            await predict();
            animationFrameId = window.requestAnimationFrame(loop);
        }
        
        // Prediction
        async function predict() {
            const { pose, posenetOutput } = await model.estimatePose(webcam.canvas);
            const prediction = await model.predict(posenetOutput);
            
            let topPrediction = { className: '-', probability: 0 };
            
            for (let i = 0; i < maxPredictions; i++) {
                const prob = prediction[i].probability;
                const percent = (prob * 100).toFixed(1);
                
                const item = labelContainer.childNodes[i];
                if (item) {
                    const nameEl = item.querySelector('.text-gray-300');
                    const valueEl = item.querySelector('.prediction-value');
                    const barEl = item.querySelector('.prediction-bar');
                    
                    if (nameEl) nameEl.textContent = prediction[i].className;
                    if (valueEl) valueEl.textContent = percent + '%';
                    if (barEl) barEl.style.width = percent + '%';
                }
                
                if (prob > topPrediction.probability) {
                    topPrediction = prediction[i];
                }
            }
            
            // Update top prediction display
            document.getElementById('top-class').textContent = topPrediction.className;
            document.getElementById('top-confidence').textContent = (topPrediction.probability * 100).toFixed(1) + '%';
            
            // Draw pose
            drawPose(pose);
        }
        
        // Draw pose on canvas
        function drawPose(pose) {
            if (webcam.canvas) {
                ctx.drawImage(webcam.canvas, 0, 0);
                if (pose) {
                    const minPartConfidence = 0.5;
                    tmPose.drawKeypoints(pose.keypoints, minPartConfidence, ctx);
                    tmPose.drawSkeleton(pose.keypoints, minPartConfidence, ctx);
                }
            }
        }
        
        // Element SDK Integration
        async function onConfigChange(newConfig) {
            config = { ...defaultConfig, ...newConfig };
            
            const systemNameEl = document.getElementById('system-name');
            if (systemNameEl) {
                systemNameEl.textContent = config.system_name || defaultConfig.system_name;
            }
            
            const footerEl = document.getElementById('footer-text');
            if (footerEl) {
                footerEl.textContent = config.footer_text || defaultConfig.footer_text;
            }
        }
        
        function mapToCapabilities(config) {
            return {
                recolorables: [],
                borderables: [],
                fontEditable: undefined,
                fontSizeable: undefined
            };
        }
        
        function mapToEditPanelValues(config) {
            return new Map([
                ['system_name', config.system_name || defaultConfig.system_name],
                ['footer_text', config.footer_text || defaultConfig.footer_text]
            ]);
        }
        
        // Initialize
        if (window.elementSdk) {
            window.elementSdk.init({
                defaultConfig,
                onConfigChange,
                mapToCapabilities,
                mapToEditPanelValues
            });
        }
    </script>
 <script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'9c0f83d713cd7334',t:'MTc2ODkyMTk5OC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>