<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>✨ Prompt Quality Evaluator</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap');

        body {
            font-family: 'Inter', sans-serif;
        }

        .fade-in {
            animation: fadeIn 0.4s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .markdown-body h1,
        .markdown-body h2,
        .markdown-body h3 {
            font-weight: 600;
        }

        ul {
            list-style: inside !important;
        }
    </style>
</head>

<body
    class="bg-gradient-to-br from-gray-800 via-gray-900 to-black text-white min-h-screen px-4 py-8 flex items-center justify-center">

    <div class="w-full max-w-7xl grid grid-cols-1 lg:grid-cols-2 gap-8" style="height: 400px !important;">

        <!-- Prompt Input Panel -->
        <div class="bg-gray-900 rounded-lg p-6 shadow-xl flex flex-col h-full">
            <h1 class="text-2xl font-bold mb-5">📝 Enter your prompt:</h1>
            <textarea id="prompt" placeholder="e.g., Build a calculator app..."
                class="w-full h-40 p-4 border border-gray-700 bg-gray-800 text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 mb-4"></textarea>
            <button id="submitPrompt"
                class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-md transition-all duration-300">
                Evaluate Prompt
            </button>
        </div>

        <!-- AI Response Panel -->
        <div class="bg-gray-900 rounded-lg p-6 shadow-xl overflow-y-auto"  style="">
            <h2 class="text-xl font-bold mb-4">📊 Prompt Evaluation:</h2>
            <div id="response" class="fade-in markdown-body prose prose-invert max-w-none text-gray-200">
                <p class="text-gray-400">Awaiting evaluation...</p>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            function sanitizeHtml(html) {
                // Basic sanitation: strip script tags to prevent XSS
                return html.replace(/<script.*?>.*?<\/script>/gi, '');
            }

            $('#submitPrompt').click(function() {
                const prompt = $('#prompt').val().trim();

                if (!prompt) {
                    $('#response').html(
                        '<p class="text-red-400">⚠️ Please enter a prompt to evaluate.</p>');
                    return;
                }

                $('#response').html('<p class="text-gray-400">⏳ Evaluating prompt...</p>');

                $.ajax({
                    url: '/prompt',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        prompt
                    },
                    success: function(data) {
                        const rawMarkdown = data.message || '⚠️ No response returned.';
                        const htmlContent = marked.parse(rawMarkdown);
                        const safeHtml = sanitizeHtml(htmlContent);

                        // Add structured response container with Tailwind spacing
                        $('#response').html(`
                            <div class="fade-in space-y-6 text-sm leading-relaxed text-gray-100">
                            ${safeHtml}
                            </div>
                        `);
                    },
                    error: function(err) {
                        const errorMsg = err?.responseJSON?.message || 'Something went wrong.';
                        $('#response').html(`<p class="text-red-500">❌ Error: ${errorMsg}</p>`);
                    }
                });
            });
        });
    </script>
</body>

</html>
