// 获取DOM元素
const singleBtn = document.getElementById('single-btn');
const multiBtn = document.getElementById('multi-btn');
const hundredBtn = document.getElementById('hundred-btn');
const resetBtn = document.getElementById('reset-btn');
const twoGeneBtn = document.getElementById('two-gene-btn');
const threeGeneBtn = document.getElementById('three-gene-btn');
const resultsList = document.getElementById('results-list');

// 结果计数器和数据
let resultCounter = 0;
let genotypeData = {};
let phenotypeData = {};
let genotypeChart = null;

// 当前基因对模式 (2或3)
let currentGeneMode = 2;

// 初始化表现型数据
function initPhenotypeData() {
    if (currentGeneMode === 2) {
        return {
            'A_B_': 0,  // 双显性
            'A_bb': 0,  // A显性B隐性
            'aaB_': 0,  // A隐性B显性
            'aabb': 0   // 双隐性
        };
    } else {
        return {
            'A_B_C_': 0,  // 三显性
            'A_B_cc': 0,  // AB显性C隐性
            'A_bbC_': 0,  // AC显性B隐性
            'A_bbcc': 0,  // A显性BC隐性
            'aaB_C_': 0,  // BC显性A隐性
            'aaB_cc': 0,  // B显性AC隐性
            'aabbC_': 0,  // C显性AB隐性
            'aabbcc': 0   // 三隐性
        };
    }
}

// 初始化表现型数据
phenotypeData = initPhenotypeData();

// 游戏状态
let isAnimating = false;

// 初始化页面
function init() {
    // 添加事件监听器
    singleBtn.addEventListener('click', startSingleCombination);
    multiBtn.addEventListener('click', startMultiCombination);
    hundredBtn.addEventListener('click', startHundredCombination);
    resetBtn.addEventListener('click', resetResults);
    twoGeneBtn.addEventListener('click', () => switchGeneMode(2));
    threeGeneBtn.addEventListener('click', () => switchGeneMode(3));
    
    // 初始化界面状态
    resetResults();
    
    // 初始化图表
    initChart();
}

// 切换基因对模式
function switchGeneMode(mode) {
    if (isAnimating) return;
    
    currentGeneMode = mode;
    
    // 更新按钮状态
    if (mode === 2) {
        twoGeneBtn.classList.add('active');
        threeGeneBtn.classList.remove('active');
    } else {
        twoGeneBtn.classList.remove('active');
        threeGeneBtn.classList.add('active');
    }
    
    // 更新显示
    document.querySelectorAll('.two-genes').forEach(el => el.classList.toggle('hidden', mode !== 2));
    document.querySelectorAll('.three-genes').forEach(el => el.classList.toggle('hidden', mode !== 3));
    
    // 重置数据
    resetResults();
}

// 初始化图表
function initChart() {
    try {
        const ctx = document.getElementById('genotype-chart').getContext('2d');
        
        // 检查Chart是否已加载
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            showPercentageTable();
            return;
        }
        
        genotypeChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: [],
                datasets: [{
                    data: [],
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
                        '#8b5cf6', '#ec4899', '#14b8a6', '#f97316',
                        '#6366f1', '#84cc16', '#ef4444', '#06b6d4'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Failed to initialize chart:', error);
        showPercentageTable();
    }
}

// 更新图表数据
function updateChart() {
    try {
        if (genotypeChart && typeof genotypeChart.update === 'function') {
            const labels = Object.keys(phenotypeData);
            const data = Object.values(phenotypeData);
            
            genotypeChart.data.labels = labels;
            genotypeChart.data.datasets[0].data = data;
            genotypeChart.update();
        } else {
            showPercentageTable();
        }
    } catch (error) {
        console.error('Failed to update chart:', error);
        showPercentageTable();
    }
}

// 显示百分比表格（备选方案）
function showPercentageTable() {
    const chartContainer = document.querySelector('.chart-container');
    if (!chartContainer) return;
    
    // 清空容器
    chartContainer.innerHTML = '';
    
    // 创建表格
    const table = document.createElement('div');
    table.className = 'percentage-table';
    
    // 计算总数
    const total = Object.values(phenotypeData).reduce((sum, count) => sum + count, 0);
    
    if (total === 0) {
        table.innerHTML = '<p class="text-gray-500 text-center py-4">暂无数据</p>';
        chartContainer.appendChild(table);
        return;
    }
    
    // 生成表格内容
    let tableHTML = '<div class="grid grid-cols-1 gap-3">';
    Object.entries(phenotypeData).forEach(([phenotype, count]) => {
        const percentage = Math.round((count / total) * 100);
        tableHTML += `
            <div class="percentage-item bg-gray-50 p-3 rounded-lg flex justify-between items-center">
                <div>
                    <span class="font-medium text-lg">${phenotype}</span>
                    <span class="text-gray-500 ml-2">${getPhenotypeDescription(phenotype)}</span>
                </div>
                <div>
                    <span class="text-blue-600 font-bold text-lg">${percentage}%</span>
                    <span class="text-gray-500 text-sm ml-1">(${count})</span>
                </div>
            </div>
        `;
    });
    tableHTML += '</div>';
    
    table.innerHTML = tableHTML;
    chartContainer.appendChild(table);
}

// 获取表现型描述
function getPhenotypeDescription(phenotype) {
    const descriptions = {
        // 两对基因表现型
        'A_B_': '双显性性状',
        'A_bb': 'A显性B隐性',
        'aaB_': 'A隐性B显性',
        'aabb': '双隐性性状',
        // 三对基因表现型
        'A_B_C_': '三显性性状',
        'A_B_cc': 'AB显性C隐性',
        'A_bbC_': 'AC显性B隐性',
        'A_bbcc': 'A显性BC隐性',
        'aaB_C_': 'BC显性A隐性',
        'aaB_cc': 'B显性AC隐性',
        'aabbC_': 'C显性AB隐性',
        'aabbcc': '三隐性性状'
    };
    return descriptions[phenotype] || '';
}

// 开始单次基因型组合过程
function startSingleCombination() {
    // 如果正在动画中，不执行任何操作
    if (isAnimating) return;
    
    isAnimating = true;
    singleBtn.disabled = true;
    multiBtn.disabled = true;
    hundredBtn.disabled = true;
    singleBtn.classList.add('loading');
    
    // 清除之前的选中状态
    clearSelections();
    
    try {
        // 获取当前模式的配子元素
        const columnGametes = document.querySelectorAll(`.column-gametes.${currentGeneMode === 2 ? 'two' : 'three'}-genes .gamete`);
        const rowGametes = document.querySelectorAll(`.row-gametes.${currentGeneMode === 2 ? 'two' : 'three'}-genes .gamete`);
        
        if (columnGametes.length === 0 || rowGametes.length === 0) {
            throw new Error('无法找到配子元素');
        }
    
        // 首先选择母本配子（走马灯效果）
        selectGameteWithMarching(columnGametes)
            .then(selectedMotherGamete => {
                // 然后选择父本配子（走马灯效果）
                return selectGameteWithMarching(rowGametes).then(selectedFatherGamete => {
                    return { mother: selectedMotherGamete, father: selectedFatherGamete };
                });
            })
            .then(gametes => {
                // 组合基因型并显示结果
                const genotype = combineGenotype(gametes.mother, gametes.father);
                addResult(genotype);
                
                // 更新基因型统计数据
                updateGenotypeData(genotype);
                
                // 恢复按钮状态
                isAnimating = false;
                singleBtn.disabled = false;
                multiBtn.disabled = false;
                hundredBtn.disabled = false;
                singleBtn.classList.remove('loading');
            })
            .catch(error => {
                console.error('组合过程中发生错误:', error);
                isAnimating = false;
                singleBtn.disabled = false;
                multiBtn.disabled = false;
                hundredBtn.disabled = false;
                singleBtn.classList.remove('loading');
                alert('操作过程中发生错误，请尝试刷新页面或重置操作。');
            });
    } catch (error) {
        console.error('单次组合初始化错误:', error);
        isAnimating = false;
        singleBtn.disabled = false;
        multiBtn.disabled = false;
        hundredBtn.disabled = false;
        singleBtn.classList.remove('loading');
        alert('操作初始化失败，请尝试刷新页面或重置操作。');
    }
}

// 开始十连基因型组合过程
function startMultiCombination() {
    // 如果正在动画中，不执行任何操作
    if (isAnimating) return;
    
    isAnimating = true;
    singleBtn.disabled = true;
    multiBtn.disabled = true;
    hundredBtn.disabled = true;
    multiBtn.classList.add('loading');
    
    // 清除之前的选中状态
    clearSelections();
    
    // 执行十次组合
    executeMultiCombination(10, multiBtn);
}

// 开始百连基因型组合过程
function startHundredCombination() {
    // 如果正在动画中，不执行任何操作
    if (isAnimating) return;
    
    isAnimating = true;
    singleBtn.disabled = true;
    multiBtn.disabled = true;
    hundredBtn.disabled = true;
    hundredBtn.classList.add('loading');
    
    // 清除之前的选中状态
    clearSelections();
    
    // 执行一百次组合
    executeMultiCombination(100, hundredBtn);
}

// 执行多次组合的通用函数
function executeMultiCombination(totalCombinations, button) {
    let combinationsLeft = totalCombinations;
    
    // 获取当前模式的配子元素
    const columnGametes = document.querySelectorAll(`.column-gametes.${currentGeneMode === 2 ? 'two' : 'three'}-genes .gamete`);
    const rowGametes = document.querySelectorAll(`.row-gametes.${currentGeneMode === 2 ? 'two' : 'three'}-genes .gamete`);
    
    if (columnGametes.length === 0 || rowGametes.length === 0) {
        console.error('无法找到配子元素');
        isAnimating = false;
        singleBtn.disabled = false;
        multiBtn.disabled = false;
        hundredBtn.disabled = false;
        button.classList.remove('loading');
        alert('无法找到配子元素，请检查页面结构。');
        return;
    }
    
    // 递归执行组合
    function executeNextCombination() {
        if (combinationsLeft <= 0) {
            // 所有组合完成
            isAnimating = false;
            singleBtn.disabled = false;
            multiBtn.disabled = false;
            hundredBtn.disabled = false;
            button.classList.remove('loading');
            return;
        }
        
        try {
            // 减少剩余组合数
            combinationsLeft--;
            
            // 使用原生随机数生成，确保统计均匀性
            const motherIndex = Math.floor(Math.random() * columnGametes.length);
            const fatherIndex = Math.floor(Math.random() * rowGametes.length);
            
            const motherGamete = columnGametes[motherIndex].getAttribute('data-gamete');
            const fatherGamete = rowGametes[fatherIndex].getAttribute('data-gamete');
            
            // 组合基因型
            const genotype = combineGenotype(motherGamete, fatherGamete);
            
            // 立即显示结果并更新统计
            addResult(genotype);
            updateGenotypeData(genotype);
            
            // 短暂延迟后执行下一次组合
            // 百连时使用更短的延迟以加快速度
            const delay = totalCombinations === 100 ? 50 : 200;
            setTimeout(executeNextCombination, delay);
        } catch (error) {
            console.error('组合执行过程中发生错误:', error);
            isAnimating = false;
            singleBtn.disabled = false;
            multiBtn.disabled = false;
            hundredBtn.disabled = false;
            button.classList.remove('loading');
            alert('组合执行过程中发生错误，请尝试刷新页面或重置操作。');
        }
    }
    
    // 开始执行组合
    executeNextCombination();
}

// 更新基因型统计数据
function updateGenotypeData(genotype) {
    // 更新基因型数据
    if (genotypeData[genotype]) {
        genotypeData[genotype]++;
    } else {
        genotypeData[genotype] = 1;
    }
    
    // 更新表现型数据
    updatePhenotypeData(genotype);
    
    // 更新图表
    updateChart();
}

// 根据基因型计算表现型并更新统计
function updatePhenotypeData(genotype) {
    if (currentGeneMode === 2) {
        // 两对基因的表现型计算
        const gene1A = genotype[0] === 'A' || genotype[1] === 'A';
        const gene2B = genotype[2] === 'B' || genotype[3] === 'B';
        
        if (gene1A && gene2B) {
            phenotypeData['A_B_']++;
        } else if (gene1A && !gene2B) {
            phenotypeData['A_bb']++;
        } else if (!gene1A && gene2B) {
            phenotypeData['aaB_']++;
        } else {
            phenotypeData['aabb']++;
        }
    } else {
        // 三对基因的表现型计算
        const gene1A = genotype[0] === 'A' || genotype[1] === 'A';
        const gene2B = genotype[2] === 'B' || genotype[3] === 'B';
        const gene3C = genotype[4] === 'C' || genotype[5] === 'C';
        
        if (gene1A && gene2B && gene3C) {
            phenotypeData['A_B_C_']++;
        } else if (gene1A && gene2B && !gene3C) {
            phenotypeData['A_B_cc']++;
        } else if (gene1A && !gene2B && gene3C) {
            phenotypeData['A_bbC_']++;
        } else if (gene1A && !gene2B && !gene3C) {
            phenotypeData['A_bbcc']++;
        } else if (!gene1A && gene2B && gene3C) {
            phenotypeData['aaB_C_']++;
        } else if (!gene1A && gene2B && !gene3C) {
            phenotypeData['aaB_cc']++;
        } else if (!gene1A && !gene2B && gene3C) {
            phenotypeData['aabbC_']++;
        } else {
            phenotypeData['aabbcc']++;
        }
    }
}

// 选择配子的动画过程（走马灯效果）
function selectGameteWithMarching(gametes) {
    return new Promise((resolve) => {
        // 使用原生随机数生成，确保统计均匀性
        const randomIndex = Math.floor(Math.random() * gametes.length);
        const selectedGamete = gametes[randomIndex];
        
        // 执行走马灯动画
        let currentIndex = 0;
        const totalSteps = 10; // 走马灯循环次数
        const stepDuration = 100; // 每步持续时间（毫秒）
        
        function executeMarchingStep() {
            // 移除前一个配子的高亮
            if (currentIndex > 0) {
                const prevIndex = (currentIndex - 1) % gametes.length;
                gametes[prevIndex].classList.remove('marching');
            }
            
            // 高亮当前配子
            const currentGamete = gametes[currentIndex % gametes.length];
            currentGamete.classList.add('marching');
            
            // 增加索引
            currentIndex++;
            
            // 继续执行下一步或结束动画
            if (currentIndex <= totalSteps) {
                setTimeout(executeMarchingStep, stepDuration);
            } else {
                // 移除所有配子的动画
                gametes.forEach(gamete => {
                    gamete.classList.remove('marching');
                });
                
                // 高亮显示选中的配子
                selectedGamete.classList.add('selected');
                
                // 获取选中配子的值
                const gameteValue = selectedGamete.getAttribute('data-gamete');
                
                // 延迟一下再返回结果，让用户看到选中效果
                setTimeout(() => {
                    resolve(gameteValue);
                }, 300);
            }
        }
        
        // 开始执行走马灯动画
        executeMarchingStep();
    });
}

// 组合基因型
function combineGenotype(motherGamete, fatherGamete) {
    if (currentGeneMode === 2) {
        // 两对基因组合，例如AB + Ab = AABb
        const gene1 = [motherGamete[0], fatherGamete[0]].sort().join('');
        const gene2 = [motherGamete[1], fatherGamete[1]].sort().join('');
        return gene1 + gene2;
    } else {
        // 三对基因组合，例如ABC + Abc = AABbCc
        const gene1 = [motherGamete[0], fatherGamete[0]].sort().join('');
        const gene2 = [motherGamete[1], fatherGamete[1]].sort().join('');
        const gene3 = [motherGamete[2], fatherGamete[2]].sort().join('');
        return gene1 + gene2 + gene3;
    }
}

// 添加结果到列表
function addResult(genotype) {
    resultCounter++;
    
    // 创建结果元素
    const resultItem = document.createElement('div');
    resultItem.className = 'result-item';
    
    // 设置结果内容
    resultItem.innerHTML = `
        <span class="result-number">#${resultCounter}</span>
        <span class="result-genotype">${genotype}</span>
    `;
    
    // 添加到结果列表的顶部
    resultsList.insertBefore(resultItem, resultsList.firstChild);
    
    // 添加进入动画
    resultItem.style.opacity = '0';
    resultItem.style.transform = 'translateX(-20px)';
    
    // 触发重排
    void resultItem.offsetWidth;
    
    // 应用动画
    resultItem.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
    resultItem.style.opacity = '1';
    resultItem.style.transform = 'translateX(0)';
}

// 清除所有选中状态
function clearSelections() {
    // 获取所有可见的配子元素
    const allGametes = document.querySelectorAll('.gamete-row:not(.hidden) .gamete');
    
    allGametes.forEach(gamete => {
        gamete.classList.remove('selected', 'highlight', 'marching');
    });
}

// 重置结果列表
function resetResults() {
    // 清除所有选中状态
    clearSelections();
    
    // 清空结果列表
    resultsList.innerHTML = '';
    
    // 重置计数器和数据
    resultCounter = 0;
    genotypeData = {};
    phenotypeData = initPhenotypeData();
    
    // 更新图表
    updateChart();
    
    // 恢复按钮状态
    isAnimating = false;
    singleBtn.disabled = false;
    multiBtn.disabled = false;
    hundredBtn.disabled = false;
    singleBtn.classList.remove('loading');
    multiBtn.classList.remove('loading');
    hundredBtn.classList.remove('loading');
}


// 页面加载完成后初始化
document.addEventListener('DOMContentLoaded', init);